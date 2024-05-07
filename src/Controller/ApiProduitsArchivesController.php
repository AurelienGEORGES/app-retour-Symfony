<?php

namespace App\Controller;

use stdClass;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiProduitsArchivesController extends AbstractController
{
    #[Route('/api/produits/archives', name: 'app_api_produits_archives')]
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

        $produits = $entityManager->getRepository(PaletteProduit::class)->findAll();
        $produitsArchivés = [];
        foreach ($produits as $produit) {
            if ($produit->getStatut() == 'archive') {
                $produitsArchivés[] = [
                    'idProduit' => $produit->getIdProduit(),
                    'codeCouleur' => $produit->getCodeCouleur(),
                    'quantite' => $produit->getQuantite(),
                    'dateReceptionne' => $produit->getDateReception(),
                ];;
            }
        }
        $response = new Response(json_encode($produitsArchivés));
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
            $secretKey = $this->getParameter('API_SECRET_KEY_PRODUITS_ARCHIVES');
            $options = new stdClass();
            $options->algorithm = 'HS256';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    } 
}
