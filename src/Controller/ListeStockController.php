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

class ListeStockController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/liste/stock', name: 'app_liste_stock')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $produitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findAll();
        foreach ($produitsPalettes as $produitPalette) {
            $palette = $produitPalette->getPalette();
        }
        $palettes = $entityManager->getRepository(Palette::class)->findAll();

        if (!empty($request)) {

            $idProduitStock = $request->query->get('recherche-stock-id-produit');
            $couleurProduitStock = $request->query->get('recherche-stock-code-couleur');
            $dateReceptionProduitStock = $request->query->get('recherche-stock-date-reception');

            $criteria = [];

            if ($idProduitStock) {
                $criteria['id_produit'] = $idProduitStock;
                $produitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByCriteria($criteria);
            }
            if ($couleurProduitStock) {
                $criteria['code_couleur'] = $couleurProduitStock;
                $produitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByCriteria($criteria);
            }
            if ($dateReceptionProduitStock) {
                $dateTime = new \DateTime($dateReceptionProduitStock);
                $formattedDate = $dateTime->format('Y-m-d H:i:s');
                $criteria['date_reception'] = $formattedDate;
                $produitsPalettes = $entityManager->getRepository(PaletteProduit::class)->findByDate($criteria);
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

        $csrfTokenProduitStock = $this->csrfTokenManager->getToken('form-stock');

        return $this->render('liste_stock/index.html.twig', [
            'controller_name' => 'ListeStockController',
            'produitsPalettes' => $produitsPalettes,
            'palette' => $palette,
            'palettes' => $palettes
        ]);
    }
}
