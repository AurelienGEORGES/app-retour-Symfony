<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Twig\Environment;
use App\Entity\Palette;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function generatePdf(Request $request, Environment $twig, EntityManagerInterface $entityManager): Response
    {

        // Validate and process form data (optional)
        $formData = $request->request->all();

        // Extract relevant data for PDF generation
        $depot = $formData['fixer-depot'];
        $statut = $formData['fixer-statut'];
        $numeroPalette = $formData['numero-palette-pdf'];
        $codeCouleur = $formData['pdf-code-couleur'];
        $produits = []; // Array to store product data

        // Process product data (assuming an ID-quantity format)
        foreach ($formData['pdf-id-produit'] as $key => $idProduit) {
            $produits[] = [
                'idProduit' => $idProduit,
                'quantite' => $formData['pdf-quantite'][$key],
            ];
        }

        // Generate PDF content (replace with your actual logic)
        $html = $twig->render('pdf/index.html.twig', [
            'depot' => $depot,
            'codeCouleur' => $codeCouleur,
            'produits' => $produits,
            'numero' => $numeroPalette
        ]);

        // Generate the PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $palette = $entityManager->getRepository(Palette::class)->find($numeroPalette);
        $statutPalette = $palette->getStatut();
        $depotPalette = $palette->getDepot();
        $currentDate = new \DateTime();
        if ($statut == 'terminée' && $statutPalette !== $statut) {
            $palette->setDateTermine($currentDate);
            $palette->setStatut($statut);
        }
        if ($statut == 'transmise' && $statutPalette !== $statut) {
            $palette->setDateTransmise($currentDate);
            $palette->setStatut($statut);
        }
        if ($depot !== $depotPalette) {
            $palette->setDepot($depot);
        }
        $entityManager->persist($palette);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'La palette bien été modifiée!'
        );

        $response = new Response();
        $response->setContent($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename=palette_' . $numeroPalette . '_negolux.pdf');
        return $response;
    }
}
