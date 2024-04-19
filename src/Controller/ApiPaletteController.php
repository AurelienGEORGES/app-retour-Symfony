<?php

namespace App\Controller;

use stdClass;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiPaletteController extends AbstractController
{
    #[Route('/api/palette', name: 'app_api_palette')]
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

        $palettes = $entityManager->getRepository(Palette::class)->findAll();

        $palettesArray = [];

        foreach ($palettes as $palette) {

            if ($palette->getStatut() == 'transmise') {

                $paletteProduitArray = [];

                $paletteProduits = $palette->getPaletteProduits();
                foreach ($paletteProduits as $paletteProduit) {
                    $paletteProduitArray[] = [
                        'idProduit' => $paletteProduit->getIdProduit(),
                        'quantite' => $paletteProduit->getQuantite(),
                    ];
                }
                $palettesArray[] = [
                    'id' => $palette->getId(),
                    'codeCouleur' => $palette->getCodeCouleur(),
                    'depot' => $palette->getDepot(),
                    'paletteProduits' => $paletteProduitArray,
                    'statut' => $palette->getStatut(),
                    'dateTransmise' => $palette->getDateTransmise()
                ];
            }
        }

        $response = new Response(json_encode($palettesArray));
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
