<?php

namespace App\Controller;

use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListePalettesController extends AbstractController
{
    #[Route('/liste/palettes', name: 'app_liste_palettes')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $palettes = $entityManager->getRepository(Palette::class)->findAll();

        if (!empty($request->query->get('recherche-palette')) || !empty($request->query->get('recherche-statut'))) {

            $idPalette = $request->query->get('recherche-palette');
            $statutPalette = $request->query->get('recherche-statut');

            $criteria = [];

            if ($idPalette) {
                $criteria['id'] = $idPalette;
                $palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }

            if ($statutPalette) {
                $criteria['statut'] = $statutPalette;
                $palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }
            
        }
        
        return $this->render('liste_palettes/index.html.twig', [
            'controller_name' => 'ListePalettesController',
            'palettes' => $palettes,
        ]);
    }
}
