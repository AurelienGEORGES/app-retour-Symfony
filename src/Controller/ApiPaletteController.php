<?php

namespace App\Controller;

use stdClass;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\Camion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiPaletteController extends AbstractController
{
    #[Route('/api/palette', name: 'app_api_palette', methods: ['GET'])]
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

        $camions = $entityManager->getRepository(Camion::class)->findAll();

        $camionsArray = [];

        foreach ($camions as $camion) {

            if ($camion->getStatut() == 'envoye' && ($camion->getDepot() == 'abérial' || $camion->getDepot() == 'philéa')) {
                $palettesCamion = $camion->getCamionPalettes();
                $palettesCamion->initialize();
                $produits = [];
                foreach ($palettesCamion as $paletteCamion) {
                    $palette = $paletteCamion->getPalette();
                    $paletteProduits = $palette->getPaletteProduits();
                    $paletteProduits->initialize();
                    foreach ($paletteProduits as $paletteProduit) {
                        $produits[] = [
                            'idProduit' => $paletteProduit->getIdProduit(),
                            'quantite' => $paletteProduit->getQuantite()
                        ];
                    }
                }
                $camionsArray[] = [
                    'id' => $camion->getId(),
                    'codeCouleur' => $camion->getCouleur(),
                    'depot' => $camion->getDepot(),
                    'paletteProduits' => $produits,
                    'statut' => $camion->getStatut(),
                    'dateTransmise' => $camion->getDateE(),
                    'commentaire' => $camion->getCommentaire()
                ];
            }
        }

        $response = new Response(json_encode($camionsArray));
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
            $secretKey = $this->getParameter('API_SECRET_KEY_PALETTES');
            $options = new stdClass();
            $options->algorithm = 'HS256';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
