<?php

namespace App\Controller;

use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListePalettesController extends AbstractController
{
    #[Route('/liste/palettes', name: 'app_liste_palettes')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $palettes = $entityManager->getRepository(Palette::class)->findAll();
        
        return $this->render('liste_palettes/index.html.twig', [
            'controller_name' => 'ListePalettesController',
            'palettes' => $palettes,
        ]);
    }
}
