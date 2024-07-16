<?php

namespace App\Controller;

use App\Entity\Palette;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeStockController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/liste/stock', name: 'app_liste_stock', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $tousProduitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findAll();
        $produitsPalettes = [];
        foreach ($tousProduitsPalettes as $produitPalette) {
            if ($produitPalette->getStatut() !== 'archive') {
                $produitsPalettes[] = $produitPalette;
            }
        }

        // modification pour récupérer uniquement les pallettes en cours ou terminées
        $allpalettesForSelect = $entityManager->getRepository(Palette::class)->findAll();
        $PalettesForSelect = [];
        foreach ($allpalettesForSelect as $Palette) {
            if ($Palette->getStatut() !== 'transmise') {
                $PalettesForSelect[] = $Palette;
            }
        }

        if (!empty($request)) {

            $idProduitStock = $request->query->get('recherche-stock-id-produit');
            $couleurProduitStock = $request->query->get('recherche-stock-code-couleur');
            $dateReceptionProduitStock = $request->query->get('recherche-stock-date-reception');

            $criteria = [];

            if ($idProduitStock) {
                $criteria['id_produit'] = $idProduitStock;
                $allproduitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByCriteria($criteria);
                $produitsPalettes = [];
                foreach ($allproduitsPalettes as $produitPalette) {
                    if ($produitPalette->getStatut() !== 'archive') {
                        $produitsPalettes[] = $produitPalette;
                    }
                }
            }
            if ($couleurProduitStock) {
                $criteria['code_couleur'] = $couleurProduitStock;
                $allproduitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByCriteria($criteria);
                $produitsPalettes = [];
                foreach ($allproduitsPalettes as $produitPalette) {
                    if ($produitPalette->getStatut() !== 'archive') {
                        $produitsPalettes[] = $produitPalette;
                    }
                }
            }
            if ($dateReceptionProduitStock) {
                $dateTime = new \DateTime($dateReceptionProduitStock);
                $formattedDate = $dateTime->format('Y-m-d H:i:s');
                $criteria['date_reception'] = $formattedDate;
                $allproduitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByDate($criteria);
                $produitsPalettes = [];
                foreach ($allproduitsPalettes as $produitPalette) {
                    if ($produitPalette->getStatut() !== 'archive') {
                        $produitsPalettes[] = $produitPalette;
                    }
                }
            }
        }

        if (!empty($request->query->get('id-produit-form-modif-palette'))) {

            $idProduitAModifier = $request->query->get('id-produit-form-modif-palette');
            $ProduitAModifier = $entityManager->getRepository(PaletteProduit::class)->find($idProduitAModifier);


            for ($p = $ProduitAModifier->getQuantite(); $p >= 1; $p--) {

                $paletteProduit = new PaletteProduit();
                $paletteId = $request->query->get('form-modif-palette_' . $p);
                $palette = $entityManager->getRepository(Palette::class)->find($paletteId);
                $codeCouleur = $palette->getCodeCouleur();
                $paletteProduit->setPalette($palette);
                $paletteProduit->setIdProduit($ProduitAModifier->getIdProduit());
                $paletteProduit->setQuantite(1);
                $paletteProduit->setCodeCouleur($codeCouleur);
                $currentDate = new \DateTime();
                $paletteProduit->setDateReception($currentDate);
                $entityManager->persist($paletteProduit);
                $entityManager->flush();
            }

            $entityManager->remove($ProduitAModifier);
            $entityManager->flush();
        }

        if (!empty($request->files->get('csv_file'))) {
            // Vérifie si un fichier a été téléchargé
            $file = $request->files->get('csv_file');

            // Lecture du contenu du fichier CSV
            $csvData = file_get_contents($file->getPathname());

            // Traitement du contenu du fichier CSV
            $lines = explode("\n", $csvData);
            foreach ($lines as $line) {
                // Ignorer les lignes vides
                if (empty(trim($line))) {
                    continue;
                }

                // Séparer la ligne en colonnes
                $rowData = str_getcsv($line);
                $produitImporter = new PaletteProduit();
                $produitImporter->setIdProduit($rowData['0']);
                $produitImporter->setQuantite($rowData['1']);
                $produitImporter->setCodeCouleur($rowData['2']);
                $paletteProduitImporter = $entityManager->getRepository(Palette::class)->find($rowData['3']);
                $produitImporter->setPalette($paletteProduitImporter);
                $currentDate = new \DateTime();
                $produitImporter->setDateReception($currentDate);
                $entityManager->persist($produitImporter);
                $entityManager->flush();    
            }
        }

        $csrfTokenProduitStock = $this->csrfTokenManager->getToken('form-stock');

        return $this->render('liste_stock/index.html.twig', [
            'controller_name' => 'ListeStockController',
            'produitsPalettes' => $produitsPalettes,
            'palettes' => $PalettesForSelect
        ]);
    }
}
