<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use App\Entity\Retour;
use DateTimeImmutable;
use App\Entity\RetourProduit;
use App\Form\SearchRetourType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeAttendusController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/liste/attendus', name: 'app_liste_attendus')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        //pour garder les paramètres de recherche entre chaques requettes (lors d'un enregistrement RETSA sur NT)
        $session = $request->getSession();

        //chargement lors de l'ouverture de la page
        $listeRetours = $entityManager->getRepository(Retour::class)->findAll();

        $secretKeyAppCommandes = $this->getParameter('API_COMMANDE_SECRET_KEY');
        $secretKeyAppRetours = $this->getParameter('API_RETOUR_SECRET_KEY');
        
        $token_RET = JWT::encode([], $secretKeyAppRetours, 'HS256');
        $token_NT = JWT::encode([], $secretKeyAppCommandes, 'HS256');
        
        $client_RET = HttpClient::create();
        $response_RET = $client_RET->request('GET', 'http://negolux.test/z/zamback/ajax/action/action.php?menu=134&nosecurity=1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_RET,
            ],
        ]);

        $client_NT = HttpClient::create();
        $response_NT = $client_NT->request('GET', 'http://negolux.test/z/zamback/ajax/action/action.php?menu=136&nosecurity=1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_NT,
            ],
        ]);

        $content_RET = $response_RET->getContent();
        $data_RET = json_decode($content_RET, true);

        $content_NT = $response_NT->getContent();
        $data_NT = json_decode($content_NT, true);


        $form = $this->createForm(SearchRetourType::class);
        $form->handleRequest($request);

        //initialisation pour la recherche dans l'API NT
        $listeRetoursNT = [];

        if ($form->isSubmitted()) {
            $dataForm = $form->getData();

            $criteria = [];
            if (!empty($dataForm['numRetour'])) {
                $criteria['num_retour'] = $dataForm['numRetour'];
            }

            if (!empty($dataForm['prenomClient'])) {
                $criteria['prenom_client'] = $dataForm['prenomClient'];
            }

            if (!empty($dataForm['nomClient'])) {
                $criteria['nom_client'] = $dataForm['nomClient'];
            }

            if (!empty($dataForm['transporteur'])) {
                $criteria['transporteur'] = $dataForm['transporteur'];
            }

            //recherche sur les RET
            $listeRetours = $entityManager->getRepository(Retour::class)->findByCriteria($criteria);

            //recherche dans l'API NT
            foreach ($data_NT as $retourNT) {
                if (!empty($dataForm['numRetour']) && strpos($retourNT['retour_NT']['ID'], $dataForm['numRetour']) !== false) {
                    $listeRetoursNT[] = $retourNT;
                }
                if (!empty($dataForm['transporteur']) && is_array($retourNT['retour_NT']['TRANSPORTEUR']) && isset($retourNT['retour_NT']['TRANSPORTEUR']['LIBELLE']) && stripos($retourNT['retour_NT']['TRANSPORTEUR']['LIBELLE'], $dataForm['transporteur']) !== false) {
                    // if (!empty($dataForm['transporteur']) && stripos($retourNT['retour_NT']['TRANSPORTEUR']['LIBELLE'], $dataForm['transporteur']) !== false) {
                    $listeRetoursNT[] = $retourNT;
                }
                if (!empty($dataForm['prenomClient']) && stripos($retourNT['retour_NT']['CLIENT']['PRENOM'], $dataForm['prenomClient']) !== false) {
                    $listeRetoursNT[] = $retourNT;
                }
                if (!empty($dataForm['nomClient']) && stripos($retourNT['retour_NT']['CLIENT']['NOM'], $dataForm['nomClient']) !== false) {
                    $listeRetoursNT[] = $retourNT;
                }
            }

            $session->set('formData', $dataForm);
        } else {
            // Si le formulaire n'a pas été soumis, utiliser les valeurs par défaut
            $dataForm = $session->get('formData', [
                'numRetour' => null,
                'prenomClient' => null,
                'nomClient' => null,
                'transporteur' => null,
            ]);
        }

        //conversion NT en RETSA et enregistrement dans la table RETOUR
        if ($request->isMethod('POST') && !empty($request->request->get('cmd_id'))) {

            $cmd_NT_to_RETSA = new Retour();
            $NumRetourRETSA = $request->request->get('cmd_id');
            $NumRetourRETSA = 'RETSA00' . $NumRetourRETSA;
            $cmd_NT_to_RETSA->setNumRetour($NumRetourRETSA);
            $cmd_NT_to_RETSA->setTransporteur($request->request->get('cmd_transpoteur'));
            $cmd_NT_to_RETSA->setNomClient($request->request->get('cmd_nom_client'));
            $cmd_NT_to_RETSA->setPrenomClient($request->request->get('cmd_prenom_client'));
            $currentDate = new \DateTime();
            $cmd_NT_to_RETSA->setDateTraitement($currentDate);
            $entityManager->persist($cmd_NT_to_RETSA);
            $entityManager->flush();

            if (!empty($request->request->all('produit_id', [])) && !empty($request->request->all('produit_qty', []))) {
                $produits_id_cmd_NT = $request->request->all('produit_id', []);
                $produits_qty_cmd_NT = $request->request->all('produit_qty', []);
                for ($i = 0; $i < count($produits_id_cmd_NT); $i++) {
                    // Créer une nouvelle instance de RetourProduit pour chaque produit
                    $produitAajouter_NT = new RetourProduit();

                    // Définir les attributs du produit avec les valeurs correspondantes
                    $produitAajouter_NT->setIdProduit($produits_id_cmd_NT[$i]);
                    $produitAajouter_NT->setQuantite($produits_qty_cmd_NT[$i]);
                    $produitAajouter_NT->setRetour($cmd_NT_to_RETSA);

                    // Persister l'entité
                    $entityManager->persist($produitAajouter_NT);
                    $entityManager->flush();
                }
            }
            if (!empty($request->request->all('composant_id', [])) && !empty($request->request->all('composant_qty', []))) {
                $composants_id_cmd_NT = $request->request->all('composant_id', []);
                $composants_qty_cmd_NT = $request->request->all('composant_qty', []);
                for ($i = 0; $i < count($composants_id_cmd_NT); $i++) {
                    // Créer une nouvelle instance de RetourProduit pour chaque produit
                    $composantAajouter_NT = new RetourProduit();

                    // Définir les attributs du produit avec les valeurs correspondantes
                    $composantAajouter_NT->setIdProduit($composants_id_cmd_NT[$i]);
                    $composantAajouter_NT->setQuantite($composants_qty_cmd_NT[$i]);
                    $composantAajouter_NT->setRetour($cmd_NT_to_RETSA);

                    // Persister l'entité
                    $entityManager->persist($composantAajouter_NT);
                    $entityManager->flush();
                }
            }
        }

        $form = $this->createForm(SearchRetourType::class, $dataForm);

        $retoursDejaEnBase = $entityManager->getRepository(Retour::class)->findAll();

        $listeNumeroRetours = [];
        foreach ($retoursDejaEnBase as $retourDejaEnBase) {
            $listeNumeroRetours[] = $retourDejaEnBase->getNumRetour();
        }

        foreach ($listeNumeroRetours as $listeNumeroRetour) {
            $finDeChaine = substr($listeNumeroRetour, -3);
            if (in_array($finDeChaine, ["-01", "-02", "-03"])) {
                $listeNumeroRetours[] = substr($listeNumeroRetour, 0, -3);
            }
        }

        foreach ($data_RET as $retourData) {

            $retour = $retourData['retour'];

            // attention traiter cas retour en -01 -02 sinon il recrée un retour
            if (!in_array($retour['numero_retour'], $listeNumeroRetours)) {

                $retourAajouter = new Retour();
                $retourAajouter->setCommentaireAutorisation($retour['commentaire_autorisation']);
                $retourAajouter->setNumRetour($retour['numero_retour']);
                $retourAajouter->setTransporteur($retour['transporteur']);
                $retourAajouter->setNomClient($retour['nom_client']);
                $retourAajouter->setPrenomClient($retour['prenom_client']);
                $dateAutorisation = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $retour['date_autorisation']);
                $retourAajouter->setDateAutorisation($dateAutorisation);
                $entityManager->persist($retourAajouter);
                $entityManager->flush();

                foreach ($retourData['produits'] as $produit) {
                    $produitAajouter = new RetourProduit();
                    $produitAajouter->setIdProduit($produit['id_produit']);
                    $produitAajouter->setQuantite($produit['quantite']);
                    $produitAajouter->setRetour($retourAajouter);
                    $entityManager->persist($produitAajouter);
                    $entityManager->flush();
                }
            }
        }

        // test retour complet ou pas
        $retourComplet = [];
        $retourProduitsReceptionnes = [];
        $retourProduits = [];

        foreach ($listeRetours as $retour) {

            $receptionnesAcomparer = [];
            $produitsReceptionnes = $retour->getRetourProduitReceptionnes();
            foreach ($produitsReceptionnes as $produitReceptionne) {
                $retourProduitsReceptionnes[] = $produitReceptionne;
                $idProduit = $produitReceptionne->getIdProduit();
                $quantite = $produitReceptionne->getQuantite();

                if (isset($receptionnesAcomparer[$idProduit])) {
                    $receptionnesAcomparer[$idProduit] += $quantite;
                } else {
                    $receptionnesAcomparer[$idProduit] = $quantite;
                }
            }

            $produitsAcomparer = [];
            $produits = $retour->getRetourProduits();
            foreach ($produits as $produit) {
                $retourProduits[] = $produit;
                $idProduit = $produit->getIdProduit();
                $quantite = $produit->getQuantite();
                $produitsAcomparer[$idProduit] = $quantite;
            }

            ksort($produitsAcomparer);
            ksort($receptionnesAcomparer);

            if ($receptionnesAcomparer === $produitsAcomparer) {
                $retourComplet[] = true; 
            } else {
                $retourComplet[] = false;
            }   
        }

        return $this->render('liste_attendus/index.html.twig', [
            'controller_name' => 'ListeAttendusController',
            'form' => $form->createView(),
            'listeRetours' => $listeRetours,
            'listeRetoursNT' => $listeRetoursNT,
            'retourComplet' => $retourComplet

        ]);
    }
}
