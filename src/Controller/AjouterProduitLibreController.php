<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Palette;
use App\Entity\ProduitLibre;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjouterProduitLibreController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/produit', name: 'app_ajouter_produit_libre')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $palettes = $entityManager->getRepository(Palette::class)->findAll();

        if ($request->isMethod('POST')) {

            $currentDate = new \DateTime();
            $idProduitLibre = $request->request->get('form-produit-libre-id');
            //$codeCouleur = $request->request->get('form-produit-libre-code-couleur');
            $quantite = $request->request->get('form-produit-libre-quantite');
            $transporteur = $request->request->get('form-produit-libre-transporteur');
            $paletteId = $request->request->get('form-produit-libre-palette');
            $palette = $entityManager->getRepository(Palette::class)->find($paletteId);
            $codeCouleur = $palette->getCodeCouleur();
            $paletteProduit = new PaletteProduit();
            $paletteProduit->setPalette($palette);
            $paletteProduit->setIdProduit($idProduitLibre);
            $paletteProduit->setQuantite($quantite);
            $paletteProduit->setCodeCouleur($codeCouleur);
            $paletteProduit->setDateReception($currentDate);
            $entityManager->persist($paletteProduit);
            $produitLibre = new ProduitLibre();
            $produitLibre->setIdproduit($idProduitLibre);
            $produitLibre->setCodeCouleur($codeCouleur);
            $produitLibre->setQuantite($quantite);
            $produitLibre->setTransporteur($transporteur);
            $entityManager->persist($produitLibre);
            $stock = new Stock();
            $stock->setIdProduit($idProduitLibre);
            $stock->setQuantite($quantite);
            $stock->setCodeCouleur($codeCouleur);
            $stock->setDateReception($currentDate);
            $entityManager->persist($stock);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le produit libre a bien été enregistré!'
            );
        }

        $csrfTokenProduitProduitLibre = $this->csrfTokenManager->getToken('form-produit-libre');

        return $this->render('ajouter_produit_libre/index.html.twig', [
            'controller_name' => 'AjouterProduitLibreController',
            'palettes' => $palettes
        ]);
    }
}
