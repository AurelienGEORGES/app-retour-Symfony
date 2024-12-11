<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Twig\Environment;
use App\Entity\Camion;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf', methods: ['GET', 'POST'])]
    public function generatePdf(Request $request, Environment $twig, EntityManagerInterface $entityManager): Response
    {

        if ($request->isMethod('POST') && !empty($request->request->get('depot-camion'))) {
            $formData = $request->request->all();

            $depot = $formData['depot-camion'];
            $idCamion = $formData['id-camion'];

            $camion = $entityManager->getRepository(Camion::class)->find($idCamion);
            $palettesCamion = $camion->getCamionPalettes();
            $palettesCamion->initialize(); 
            $palette = [];
            foreach ($palettesCamion as $paletteCamion) {
                $palette = $paletteCamion->getPalette();
                $paletteProduits = $palette->getPaletteProduits();
                $paletteProduits->initialize();
                $numeroPalette = $palette->getId();
                foreach ($paletteProduits as $paletteProduit) {
                    $produits[$numeroPalette][] = [
                        'idProduit' => $paletteProduit->getIdProduit(),
                        'quantite' => $paletteProduit->getQuantite(),
                        'codeCouleur' => $paletteProduit->getCodeCouleur(),
                        'idProduitAarchiver' => $paletteProduit->getId()
                    ];
                }
            }

            $html = $twig->render('pdf/index.html.twig', [
                'depot' => $depot,
                'codeCouleur' => $camion->getCouleur(),
                'produits' => $produits,
                'numero' => $idCamion
            ]);

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();

            $currentDate = new \DateTime();
            $camion->setDateE($currentDate);
            $camion->setStatut('envoye');
            $camion->setDepot($depot);
            $entityManager->persist($camion);
            $entityManager->flush();    
        }

        $this->addFlash(
            'notice',
            'Le camion a bien été envoyé!'
        );

        if ($camion->getStatut() === 'envoye') {

            foreach ($produits as $paletteProduits) {

                foreach ($paletteProduits as $produitAarchiver) {
                    $produitAmodifierLeStatut = $entityManager->getRepository(PaletteProduit::class)->find($produitAarchiver['idProduitAarchiver']);
                    $produitAmodifierLeStatut->setStatut('archive');
                    $entityManager->persist($produitAmodifierLeStatut);
                }
            }
            $entityManager->flush();

            $response = new Response();
            $response->setContent($dompdf->output());
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename=camion_' . $idCamion . '_negolux.pdf');

            return $response;
            
        } else {
            return $this->redirectToRoute('app_liste_camion');
        }
    }
}
