<?php

namespace App\Controller;

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
        $listeRetours = $entityManager->getRepository(Retour::class)->findAll();

        // $retours = $entityManager->getRepository(Retour::class)->findAll();

        // $retourProduits = [];
        // foreach ($retours as $retour) {
        //     $retourProduits = array_merge($retourProduits, $retour->getRetourProduits()->toArray());
        // }

        $form = $this->createForm(SearchRetourType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            $criteria = [];
            if (!empty($data['numRetour'])) {
                $criteria['num_retour'] = $data['numRetour'];
            }

            if (!empty($data['prenomClient'])) {
                $criteria['prenom_client'] = $data['prenomClient'];
            }

            if (!empty($data['nomClient'])) {
                $criteria['nom_client'] = $data['nomClient'];
            }

            if (!empty($data['transporteur'])) {
                $criteria['transporteur'] = $data['transporteur'];
            }
            $listeRetours = $entityManager->getRepository(Retour::class)->findByCriteria($criteria);
        } else {
            $listeRetours = $entityManager->getRepository(Retour::class)->findAll();
        }

        // Créer une instance de HttpClient
        $client = HttpClient::create();

        // Envoyer une requête GET à l'URL spécifiée
        $response = $client->request('GET', 'http://negolux.test/z/zamback/ajax/action/action.php?menu=134&nosecurity=1');

        // Récupérer le contenu de la réponse
        $content = $response->getContent();

        // Décoder le contenu JSON en tableau associatif
        $data = json_decode($content, true);

        $retoursDejaEnBase = $entityManager->getRepository(Retour::class)->findAll();

        $listeNumeroRetours = [];
        foreach ($retoursDejaEnBase as $retourDejaEnBase) {
            $listeNumeroRetours[] = $retourDejaEnBase->getNumRetour();
        }

        foreach ($listeNumeroRetours as $listeNumeroRetour) {
            $finDeChaine = substr($listeNumeroRetour, -3);
            if (!in_array($finDeChaine, ["-01", "-02", "-03"])) {
                $listeNumeroRetours[] = $listeNumeroRetour;
            }
        }    

        foreach ($data as $retourData) {

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

                foreach ($retourData['produits'] as $produit) {
                    $produitAajouter = new RetourProduit();
                    $produitAajouter->setIdProduit($produit['id_produit']);
                    $produitAajouter->setQuantite($produit['quantite']);
                    $produitAajouter->setRetour($retourAajouter);
                    $entityManager->persist($produitAajouter);
                }
            }
        }
        $entityManager->flush();

        

        return $this->render('liste_attendus/index.html.twig', [
            'controller_name' => 'ListeAttendusController',
            'form' => $form->createView(),
            // 'retours' => $retours,
            // 'retourProduits' => $retourProduits
            // 'data' => $data
            'listeRetours' => $listeRetours

        ]);
    }
}
