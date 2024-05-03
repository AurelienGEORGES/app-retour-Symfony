<?php

namespace App\Controller;


use App\Entity\Stock;
use App\Entity\Palette;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreationPaletteController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/creation/palette', name: 'app_creation_palette')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!empty($request->query->get('choix-couleur-palette'))) {

            $codeCouleurPalette = $request->query->get('choix-couleur-palette');
            $palette = new Palette();
            $palette->setCodeCouleur($codeCouleurPalette);
            $palette->setStatut('en cours');
            $entityManager->persist($palette);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'La palette a bien été crée!'
            );
        }

        $csrfTokenPaletteCouleur = $this->csrfTokenManager->getToken('form-palette-couleur-token');

        return $this->render('creation_palette/index.html.twig', [
            'controller_name' => 'CreationPaletteController',
        ]);
    }
}
