<?php

namespace App\Controller;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    //#[IsGranted("ROLE_USER")]
    #[Route('/api', name: 'app_api')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $retours = $entityManager->getRepository(Retour::class)->findAll();

        $retoursArray = [];

        foreach ($retours as $retour) {

            $retourProduitArray = [];
            $commandeProduitArray = [];
            $retourComplet = '';

            $retourProduits = $retour->getRetourProduitReceptionnes();
            foreach ($retourProduits as $retourProduit) {
                $retourProduitArray[] = [
                    'idProduit' => $retourProduit->getIdProduit(),
                    'quantite' => $retourProduit->getQuantite(),
                ];
            }

            $commandeProduits = $retour->getRetourProduits();
            foreach ($commandeProduits as $commandeProduit) {
                $commandeProduitArray[] = [
                    'idProduit' => $commandeProduit->getIdProduit(),
                    'quantite' => $commandeProduit->getQuantite(),
                ];
            }

            $bordereau = $retour->getBordereau();
            $photoBordereau = $bordereau !== null ? $bordereau->getPhoto1() : null;

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
                $retourComplet = 'complet';
            } else {
                $retourComplet = 'incomplet';
            }

            $retoursArray[] = [
                'numRetour' => $retour->getNumRetour(),
                'dateAutorisation' => $retour->getDateAutorisation(),
                'nomClient' => $retour->getNomClient(),
                'prenomClient' => $retour->getPrenomClient(),
                'transporteur' => $retour->getTransporteur(),
                'dateTraitement' => $retour->getDateTraitement(),
                'etat' => $retour->getEtat(),
                'commentaire' => $retour->getCommentaire(),
                'photo1' => $retour->getPhoto1(),
                'photo2' => $retour->getPhoto2(),
                'photo3' => $retour->getPhoto3(),
                'photo4' => $retour->getPhoto4(),
                'photo5' => $retour->getPhoto5(),
                'retourProduits' => $retourProduitArray,
                'commandeProduits' => $commandeProduitArray,
                'bordereau' => $photoBordereau,
                'statut' => $retourComplet
            ];
        }

        // Convertir le tableau en JSON et le renvoyer dans la réponse
        $response = new Response(json_encode($retoursArray));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
