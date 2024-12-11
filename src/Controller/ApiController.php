<?php

namespace App\Controller;

use stdClass;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->getRequestToken($request);

        if (!$token) {
            return new JsonResponse(['error' => 'Token manquant'], Response::HTTP_UNAUTHORIZED);
        }

        $decoded = $this->verifyToken($token);
        if (!$decoded) {
            return new JsonResponse(['error' => 'Token invalide'], Response::HTTP_UNAUTHORIZED);
        }

        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 15);
        $offset = ($page - 1) * $limit;

        $criteria = [
            'numRetour' => $request->query->get('numRetour', ''),
            'prenomClient' => $request->query->get('prenomClient', ''),
            'nomClient' => $request->query->get('nomClient', ''),
            'transporteur' => $request->query->get('transporteur', ''),
            'dateAutorisationDebut' => $request->query->get('dateAutorisationDebut', ''),
            'dateAutorisationFin' => $request->query->get('dateAutorisationFin', ''),
            'dateReceptionDebut' => $request->query->get('dateReceptionDebut', ''),
            'dateReceptionFin' => $request->query->get('dateReceptionFin', ''),
            'etatColis' => $request->query->get('etatColis', ''),
            'etatProduit' => $request->query->get('etatProduit', ''),
        ];
        // dd($page);
        if (!empty(array_filter($criteria))) {
            $repository = $entityManager->getRepository(Retour::class);
            $retours = $repository->searchRetours($criteria, $limit, $offset);
            $total = $repository->countRetours($criteria);
        } else if (empty(array_filter($criteria))) {
            $retours = $entityManager->getRepository(Retour::class)->findBy([], null, $limit, $offset);
            $total = $entityManager->getRepository(Retour::class)->count([]);
        }
        if ($request->query->get('export') == 'export') {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }
        if ($request->query->get('retours_archivés') == 'retours_archivés') {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }
        if ($request->query->get('produitsRetours') == 'produitsRetours') {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }
        if ($request->query->get('retoursCommande') == 'retoursCommande') {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }
        if ($request->query->get('exportRetoursArchivés') == 'exportRetoursArchivés') {
            $retours = $entityManager->getRepository(Retour::class)->findAll();
        }
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
                    'codeCouleur' => $retourProduit->getCodeCouleur(),
                    'transporteur' => $retour->getTransporteur(),
                    'dateReception' => $retourProduit->getDateReception(),
                    'numRetour' => $retour->getNumRetour()
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
                $retourComplet = 'différent';
            }

            $retoursArray[] = [
                'numRetour' => $retour->getNumRetour(),
                'dateAutorisation' => $retour->getDateAutorisation(),
                'nomClient' => $retour->getNomClient(),
                'prenomClient' => $retour->getPrenomClient(),
                'transporteur' => $retour->getTransporteur(),
                'dateTraitement' => $retour->getDateTraitement(),
                'dateTraitement2' => $retour->getDateTraitement02(),
                'dateTraitement3' => $retour->getDateTraitement03(),
                'etat1' => $retour->getEtat(),
                'etat2' => $retour->getEtat02(),
                'etat3' => $retour->getEtat03(),
                'etatProduit1' => $retour->getEtatProduit(),
                'etatProduit2' => $retour->getEtatProduit02(),
                'etatProduit3' => $retour->getEtatProduit03(),
                'commentaire' => $retour->getCommentaire(),
                'photo1' => $retour->getPhoto1(),
                'idProduitPhoto1' => $retour->getIdProduitPhoto1(),
                'photo2' => $retour->getPhoto2(),
                'idProduitPhoto2' => $retour->getIdProduitPhoto2(),
                'photo3' => $retour->getPhoto3(),
                'idProduitPhoto3' => $retour->getIdProduitPhoto3(),
                'photo4' => $retour->getPhoto4(),
                'idProduitPhoto4' => $retour->getIdProduitPhoto4(),
                'photo5' => $retour->getPhoto5(),
                'idProduitPhoto5' => $retour->getIdProduitPhoto5(),
                'retourProduits' => $retourProduitArray,
                'commandeProduits' => $commandeProduitArray,
                'bordereau' => $photoBordereau,
                'statut' => $retourComplet
            ];
        }

        $responseData = [
            'data' => $retoursArray, // Vos données existantes
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
            ],
        ];

        // $response = new Response(json_encode($retoursArray));
        $response = new Response(json_encode($responseData));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    private function getRequestToken(Request $request): ?string
    {
        $token = $request->headers->get('Authorization');

        if (!$token || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function verifyToken(string $token): ?object
    {
        try {
            $secretKey = $this->getParameter('API_SECRET_KEY');
            $options = new stdClass();
            $options->algorithm = 'HS256';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
