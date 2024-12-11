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
            if ($Palette->getStatut() !== 'transmise' && $Palette->getStatut() !== 'camion') {
                $Palettes[] = $Palette;
            }
        }

        if (!empty($request->query->get('recherche-palette')) || !empty($request->query->get('recherche-statut'))) {

            $idPalette = $request->query->get('recherche-palette');
            $statutPalette = $request->query->get('recherche-statut');

            $criteria = [];

            if ($idPalette) {
                $criteria['id'] = $idPalette;
                $Palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }

            if ($statutPalette) {
                $criteria['statut'] = $statutPalette;
                $Palettes = $entityManager->getRepository(Palette::class)->findByCriteria($criteria);
            }
        }

        $allpalettesForSelect = $entityManager->getRepository(Palette::class)->findAll();
        $PalettesForSelect = [];
        foreach ($allpalettesForSelect as $Palette) {
            if ($Palette->getStatut() !== 'camion' ) {
                $PalettesForSelect[] = $Palette;
            }
        }

        if ($request->isMethod('POST') && !empty($request->request->get('fixer-statut'))) {

            $formData = $request->request->all();

            $statut = $formData['fixer-statut'];
            $numeroPalette = $formData['numero-palette'];

            $palette = $entityManager->getRepository(Palette::class)->find($numeroPalette);
            $statutPalette = $palette->getStatut();
            $currentDate = new \DateTime();
            if ($statut == 'terminée' && $statutPalette !== $statut) {
                $palette->setDateTermine($currentDate);
                $palette->setStatut($statut);
            }
            if ($statut == 'transmise' && $statutPalette !== $statut) {
                $palette->setDateTransmise($currentDate);
                $palette->setStatut($statut);
            }
            $entityManager->persist($palette);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'La palette bien été modifiée!'
            );
        }

        return $this->render('liste_palettes/index.html.twig', [
            'controller_name' => 'ListePalettesController',
            'palettes' => $Palettes,
            'palettesSelect' => $PalettesForSelect
        ]);
    }
}
