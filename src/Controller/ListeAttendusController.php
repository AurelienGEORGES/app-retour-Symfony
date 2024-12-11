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
    #[Route('/liste/attendus', name: 'app_liste_attendus', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();

        $listeRetours = [];
        $payload_NT = [];
        $payload_RET = [];
        $payload_RET['prenomClient'] = '';
        $payload_NT['prenomClient'] = '';
        $payload_RET['nomClient'] = '';
        $payload_NT['nomClient'] = '';
        $payload_RET['numRetour'] = '';
        $payload_NT['numRetour'] = '';
        $payload_RET['transporteur'] = '';
        $payload_NT['transporteur'] = '';
        if ($request->get('search_retour')) {
            $payload_RET = $request->get('search_retour');
        }

        if ($request->get('search_retour')) {
            $payload_NT = $request->get('search_retour');
        }
        
        $payload_RET['menu'] = 134;
        $payload_NT['menu'] = 136;
        
        $payload_NT['nosecurity'] = 1;
        $payload_RET['nosecurity'] = 1;

        $secretKeyAppCommandes = $this->getParameter('API_COMMANDE_SECRET_KEY');
        $secretKeyAppRetours = $this->getParameter('API_RETOUR_SECRET_KEY');

        $token_RET = JWT::encode($payload_RET, $secretKeyAppRetours, 'HS256');
        $token_NT = JWT::encode($payload_NT, $secretKeyAppCommandes, 'HS256');
        
        $client_RET = HttpClient::create();
        $response_RET = $client_RET->request('GET', $this->getParameter('URL_API_ERP') . 'menu=' . $payload_RET['menu'] . '&nosecurity=' . $payload_RET['nosecurity'] . '&prenomClient=' . $payload_RET['prenomClient'] . '&nomClient=' . $payload_RET['nomClient'] . '&numRetour=' . $payload_RET['numRetour'] . '&transporteur=' . $payload_RET['transporteur'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_RET,
                'Content-Type' => 'application/json'
            ],
        ]);

        $client_NT = HttpClient::create();
        $response_NT = $client_NT->request('GET', $this->getParameter('URL_API_ERP') . 'menu=' . $payload_NT['menu'] . '&nosecurity=' . $payload_NT['nosecurity'] . '&prenomClient=' . $payload_NT['prenomClient'] . '&nomClient=' . $payload_NT['nomClient'] . '&numRetour=' . $payload_NT['numRetour'] . '&transporteur=' . $payload_NT['transporteur'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token_NT,
                'Content-Type' => 'application/json'
            ],
        ]);
        
        $content_RET = $response_RET->getContent();
        $data_RET = json_decode($content_RET, true);
        
        $content_NT = $response_NT->getContent();
        $data_NT = json_decode($content_NT, true);
        
        $form = $this->createForm(SearchRetourType::class);
        $form->handleRequest($request);

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

            $listeRetours = $entityManager->getRepository(Retour::class)->findByCriteria($criteria);

            $session->set('formData', $dataForm);
        } else {
            
            $dataForm = $session->get('formData', [
                'numRetour' => null,
                'prenomClient' => null,
                'nomClient' => null,
                'transporteur' => null,
            ]);
        }

        if ($request->isMethod('POST') && !empty($request->request->get('cmd_id'))) {

            $criteria0['num_retour'] = 'RETSA00' . $request->request->get('cmd_id');
            $criteria1['num_retour'] = 'RETSA00' . $request->request->get('cmd_id') . '-01';
            $criteria2['num_retour'] = 'RETSA00' . $request->request->get('cmd_id') . '-02';
            $criteria3['num_retour'] = 'RETSA00' . $request->request->get('cmd_id') . '-03';
            $criteria4['num_retour'] = 'RET00' . $request->request->get('cmd_id');
            $criteria5['num_retour'] = 'RET00' . $request->request->get('cmd_id') . '-01';
            $criteria6['num_retour'] = 'RET00' . $request->request->get('cmd_id') . '-02';
            $criteria7['num_retour'] = 'RET00' . $request->request->get('cmd_id') . '-03';
            $RetourExistant0 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria0);
            $RetourExistant1 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria1);
            $RetourExistant2 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria2);
            $RetourExistant3 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria3);
            $RetourExistant4 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria4);
            $RetourExistant5 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria5);
            $RetourExistant6 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria6);
            $RetourExistant7 = $entityManager->getRepository(Retour::class)->findByCriteria($criteria7);

            if (
                empty($RetourExistant0) && empty($RetourExistant1) && empty($RetourExistant2) && empty($RetourExistant3)
                && empty($RetourExistant4) && empty($RetourExistant5) && empty($RetourExistant6) && empty($RetourExistant7)
            ) {

                $cmd_NT_to_RETSA = new Retour();
                $NumRetourRETSA = $request->request->get('cmd_id');
                $NumRetourRETSA = 'RETSA00' . $NumRetourRETSA;
                $cmd_NT_to_RETSA->setNumRetour($NumRetourRETSA);
                $cmd_NT_to_RETSA->setTransporteur($request->request->get('cmd_transpoteur'));
                $cmd_NT_to_RETSA->setNomClient($request->request->get('cmd_nom_client'));
                $cmd_NT_to_RETSA->setPrenomClient($request->request->get('cmd_prenom_client'));
                $entityManager->persist($cmd_NT_to_RETSA);
                $entityManager->flush();

                if (!empty($request->request->all('produit_id', [])) && !empty($request->request->all('produit_qty', []))) {
                    $produits_id_cmd_NT = $request->request->all('produit_id', []);
                    $produits_qty_cmd_NT = $request->request->all('produit_qty', []);
                    for ($i = 0; $i < count($produits_id_cmd_NT); $i++) {
                        $produitAajouter_NT = new RetourProduit();
                        $produitAajouter_NT->setIdProduit($produits_id_cmd_NT[$i]);
                        $produitAajouter_NT->setQuantite($produits_qty_cmd_NT[$i]);
                        $produitAajouter_NT->setRetour($cmd_NT_to_RETSA);
                        $entityManager->persist($produitAajouter_NT);
                        $entityManager->flush();
                    }
                }
                if (!empty($request->request->all('composant_id', [])) && !empty($request->request->all('composant_qty', []))) {
                    $composants_id_cmd_NT = $request->request->all('composant_id', []);
                    $composants_qty_cmd_NT = $request->request->all('composant_qty', []);
                    for ($i = 0; $i < count($composants_id_cmd_NT); $i++) {
                        $composantAajouter_NT = new RetourProduit();
                        $composantAajouter_NT->setIdProduit($composants_id_cmd_NT[$i]);
                        $composantAajouter_NT->setQuantite($composants_qty_cmd_NT[$i]);
                        $composantAajouter_NT->setRetour($cmd_NT_to_RETSA);
                        $entityManager->persist($composantAajouter_NT);
                        $entityManager->flush();
                    }
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

        if ($data_RET != NULL) {
            foreach ($data_RET as $retourData) {

                $retour = $retourData['retour'];

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
        }
        
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
            'listeRetoursNT' => $data_NT,
            'retourComplet' => $retourComplet
        ]);
    }
}
