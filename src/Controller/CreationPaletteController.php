<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Palette;
use App\Entity\ProduitLibre;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreationPaletteController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/creation/palette', name: 'app_creation_palette')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $stockProduits = $entityManager->getRepository(Stock::class)->findAll();

        if (!empty($request->query->get('recherche-creation-id-produit')) || !empty($request->query->get('recherche-creation-code-couleur'))) {

            $idProduitStock = $request->query->get('recherche-creation-id-produit');
            $couleurProduitStock = $request->query->get('recherche-creation-code-couleur');

            $criteria = [];
            if ($idProduitStock) {
                $criteria['id_produit'] = $idProduitStock;
                $stockProduits = $entityManager->getRepository(Stock::class)->findByCriteria($criteria);
            }

            if ($couleurProduitStock) {
                $criteria['code_couleur'] = $couleurProduitStock;
                $stockProduits = $entityManager->getRepository(Stock::class)->findByCriteria($criteria);
            }
        }

        $produitsSelectionnes = array();

        $request->getSession()->start();
        $produitsSelectionnes = $request->getSession()->get('produits_selectionnes', []);

        if (!empty($request->query->get('quantite-produit-palette'))) {
            $produitsSelectionnes[] = [
                'qte' => $request->query->get('quantite-produit-palette'),
                'prod' => $request->query->get('produit-palette'),
                'idprod' => $request->query->get('produit-id-palette')
            ];

            $quantite = $request->query->get('quantite-produit-palette');
            $produitId = $request->query->get('produit-palette');

            $stockProduit = $entityManager->getRepository(Stock::class)->find($produitId);

            if ($stockProduit) {

                $nouvelleQuantite = $stockProduit->getQuantite() - $quantite;
                $stockProduit->setQuantite($nouvelleQuantite);

                $entityManager->persist($stockProduit);
                $entityManager->flush();
            }
        }

        if (!empty($request->query->get('produit-qte'))) {

            $quantite = $request->query->get('produit-qte');
            $produitId = $request->query->get('produit-prod');

            $stockProduit = $entityManager->getRepository(Stock::class)->find($produitId);

            if ($stockProduit) {

                $nouvelleQuantite = $stockProduit->getQuantite() + $quantite;
                $stockProduit->setQuantite($nouvelleQuantite);

                $entityManager->persist($stockProduit);
                $entityManager->flush();
            }

            foreach ($produitsSelectionnes as $key => $produit) {
                if ($produit['prod'] === $produitId) {
                    unset($produitsSelectionnes[$key]);
                    break;
                }
            }
        }

        if (!empty($request->query->get('choix-couleur-palette')) && !empty($request->query->get('choix-depot-palette'))) {

            $codeCouleurPalette = $request->query->get('choix-couleur-palette');
            $depotPalette = $request->query->get('choix-depot-palette');
            $palette = new Palette();
            $palette->setCodeCouleur($codeCouleurPalette);
            $palette->setDepot($depotPalette);
            $entityManager->persist($palette);

            foreach ($produitsSelectionnes as $produitSelectionne) {
                $paletteProduit = new PaletteProduit();
                $paletteProduit->setIdProduit($produitSelectionne['idprod']);
                $paletteProduit->setQuantite($produitSelectionne['qte']);
                $paletteProduit->setPalette($palette);
                $entityManager->persist($paletteProduit);
            }

            $produitsSelectionnes = array();

            $stockProduits = $entityManager->getRepository(Stock::class)->findAll();
            foreach ($stockProduits as $stockProduit)
                if ($stockProduit->getQuantite() === 0) {
                    $entityManager->remove($stockProduit);
                }

            $entityManager->flush();

            $this->addFlash(
                'notice',
                'La palette a bien été envoyée!'
            );

            $stockProduits = $entityManager->getRepository(Stock::class)->findAll();
        }

        $request->getSession()->set('produits_selectionnes', $produitsSelectionnes);

        return $this->render('creation_palette/index.html.twig', [
            'controller_name' => 'CreationPaletteController',
            'produitsSelectionnes' => $produitsSelectionnes,
            'stockProduits' => $stockProduits,

        ]);
    }
}
