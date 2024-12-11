<?php

namespace App\Controller;

use App\Entity\Camion;
use App\Entity\Palette;
use App\Entity\CamionPalette;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CamionController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/camion', name: 'app_camion')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $allpalettes = $entityManager->getRepository(Palette::class)->findAll();
        $Palettes = [];
        foreach ($allpalettes as $Palette) {
            if ($Palette->getStatut() == 'transmise') {
                $Palettes[] = $Palette;
            }
        }

        if ($request->isMethod('POST') && !empty($request->request->all('palettes', []))) {

            $camion = new Camion();
            $commentaireCamion = $request->request->get('commentaire-form-camion');
            $camion->setCommentaire($commentaireCamion);
            $currentDate = new \DateTime();
            $camion->setDateC($currentDate);
            $couleurCamion = $request->request->get('code-couleur-camion');
            $camion->setCouleur($couleurCamion);
            $camion->setStatut('cree');
            $entityManager->persist($camion);
            $selectedPalettes = $request->request->all('palettes', []);
            
            foreach ($selectedPalettes as $selectedPaletteId) {
                $palette = $entityManager->getRepository(Palette::class)->find($selectedPaletteId);
                if ($palette) {
                    $palette->setStatut('camion');
                    $entityManager->persist($palette);
                    $camionPalette = new CamionPalette();
                    $camionPalette->setPalette($palette);
                    $camionPalette->setCamion($camion);  
                    $entityManager->persist($camionPalette);
                }
            }
            
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le camion a bien été enregistré!'
            );
        }

        $csrfTokenCamion = $this->csrfTokenManager->getToken('form-camion-token');

        return $this->render('camion/index.html.twig', [
            'controller_name' => 'CamionController',
            'palettes' => $Palettes,
        ]);
    }
}
