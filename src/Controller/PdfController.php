<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function generatePdf(Request $request, Environment $twig): Response
    {

        // Validate and process form data (optional)
        $formData = $request->request->all();

        // Extract relevant data for PDF generation
        $depot = $formData['pdf-depot'];
        $numeroPalette = $formData['numero-palette'];
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

        // Prepare the PDF download response
        $response = new Response();
        $response->setContent($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename=palette_negolux.pdf');

        return $response;
    }
}
