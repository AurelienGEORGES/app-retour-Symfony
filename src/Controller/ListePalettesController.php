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
    #[Route('/liste/palettes', name: 'app_liste_palettes', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $allpalettes = $entityManager->getRepository(Palette::class)->findAll();
        $Palettes = [];
        foreach ($allpalettes as $Palette) {
            if ($Palette->getStatut() !== 'transmise') {
                $Palettes[] = $Palette;
            }
        }

        if (!empty($request->query->get('recherche-palette')) || !empty($request->query->get('recherche-statut')) || !empty($request->query->get('recherche-depot'))) {

            $idPalette = $request->query->get('recherche-palette');
            $statutPalette = $request->query->get('recherche-statut');
            $depotPalette = $request->query->get('recherche-depot');

            $criteria = [];

            if ($idPalette) {
                $criteria['id'] = $idPalette;
                $Palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }

            if ($statutPalette) {
                $criteria['statut'] = $statutPalette;
                $Palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }

            if ($depotPalette) {
                $criteria['depot'] = $depotPalette;
                $Palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }
            
        }

        $allpalettesForSelect = $entityManager->getRepository(Palette::class)->findAll();
        $PalettesForSelect = [];
        foreach ($allpalettesForSelect as $Palette) {
            if ($Palette->getStatut() !== 'transmise') {
                $PalettesForSelect[] = $Palette;
            }
        }
        
        return $this->render('liste_palettes/index.html.twig', [
            'controller_name' => 'ListePalettesController',
            'palettes' => $Palettes,
            'palettesSelect' => $PalettesForSelect
        ]);
    }
}
