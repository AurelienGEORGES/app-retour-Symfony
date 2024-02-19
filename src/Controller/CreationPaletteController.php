<?php

namespace App\Controller;

use App\Entity\Palette;
use App\Entity\PaletteProduit;
use App\Entity\ProduitLibre;
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

        $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findAll();
        $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findAll();

        if (!empty($request->query->get('recherche-creation-id-produit')) || !empty($request->query->get('recherche-creation-code-couleur'))) {

            $idProduitStock = $request->query->get('recherche-creation-id-produit');
            $couleurProduitStock = $request->query->get('recherche-creation-code-couleur');

            $criteriaId = [];
            if ($idProduitStock) {
                $criteriaId['id_produit'] = $idProduitStock;
                $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findByCriteria($criteriaId);
                $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findByCriteria($criteriaId);
            }
            $criteriaCouleur = [];
            if ($couleurProduitStock) {
                $criteriaCouleur['code_couleur'] = $couleurProduitStock;
                $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findByCriteria($criteriaCouleur);
                $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findByCriteria($criteriaCouleur);
            }
        }

        $produitsSelectionnes = array();

        $request->getSession()->start();
        $produitsSelectionnes = $request->getSession()->get('produits_selectionnes', []);

        if (!empty($request->query->get('quantite-produit-palette-receptionne'))) {
            $produitsSelectionnes[] = [
                'qte' => $request->query->get('quantite-produit-palette-receptionne'),
                'prod' => $request->query->get('produit-palette-receptionne'),
                'idprod' => $request->query->get('produit-id-palette-receptionne'),
                'type' => $request->query->get('produit-palette-receptionne-type')
            ];

            $quantite = $request->query->get('quantite-produit-palette-receptionne');
            $produitId = $request->query->get('produit-palette-receptionne');

            $stockProduitReceptionne = $entityManager->getRepository(RetourProduitReceptionnes::class)->find($produitId);

            if ($stockProduitReceptionne) {

                $nouvelleQuantite = $stockProduitReceptionne->getQuantite() - $quantite;
                $stockProduitReceptionne->setQuantite($nouvelleQuantite);

                $entityManager->persist($stockProduitReceptionne);
                $entityManager->flush();
            }
        }

        if (!empty($request->query->get('quantite-produit-palette-libre'))) {
            $produitsSelectionnes[] = [
                'qte' => $request->query->get('quantite-produit-palette-libre'),
                'prod' => $request->query->get('produit-palette-libre'),
                'idprod' => $request->query->get('produit-id-palette-libre'),
                'type' => $request->query->get('produit-palette-libre-type')
            ];

            $quantite = $request->query->get('quantite-produit-palette-libre');
            $produitId = $request->query->get('produit-palette-libre');

            $stockProduitLibre = $entityManager->getRepository(ProduitLibre::class)->find($produitId);

            if ($stockProduitLibre) {

                $nouvelleQuantite = $stockProduitLibre->getQuantite() - $quantite;
                $stockProduitLibre->setQuantite($nouvelleQuantite);

                $entityManager->persist($stockProduitLibre);
                $entityManager->flush();
            }
        }

        if (!empty($request->query->get('produit-qte'))) {

            $quantite = $request->query->get('produit-qte');
            $produitId = $request->query->get('produit-prod');

            if ($request->query->get('produit-type') == 'réceptionné') {

                $stockProduitReceptionne = $entityManager->getRepository(RetourProduitReceptionnes::class)->find($produitId);

                if ($stockProduitReceptionne) {

                    $nouvelleQuantite = $stockProduitReceptionne->getQuantite() + $quantite;
                    $stockProduitReceptionne->setQuantite($nouvelleQuantite);

                    $entityManager->persist($stockProduitReceptionne);
                    $entityManager->flush();
                }
            } else {
                $stockProduitLibre = $entityManager->getRepository(ProduitLibre::class)->find($produitId);

                if ($stockProduitLibre) {

                    $nouvelleQuantite = $stockProduitLibre->getQuantite() + $quantite;
                    $stockProduitLibre->setQuantite($nouvelleQuantite);

                    $entityManager->persist($stockProduitLibre);
                    $entityManager->flush();
                }
            }

            foreach ($produitsSelectionnes as $key => $produit) {
                if ($produit['prod'] === $produitId) {
                    unset($produitsSelectionnes[$key]);
                    break;
                }
            }
        }


        //$request->getSession()->set('produits_selectionnes', $produitsSelectionnes);

        if (!empty($request->query->get('choix-couleur-palette')) && !empty($request->query->get('choix-depot-palette'))) {

            $codeCouleurPalette = $request->query->get('choix-couleur-palette');
            $depotPalette = $request->query->get('choix-depot-palette');
            $palette = new Palette();
            $palette->setCodeCouleur($codeCouleurPalette);
            $palette->setDepot($depotPalette);
            $entityManager->persist($palette);
            
            foreach($produitsSelectionnes as $produitSelectionne) {
                $paletteProduit = new PaletteProduit();
                $paletteProduit->setIdProduit($produitSelectionne['idprod']);
                $paletteProduit->setQuantite($produitSelectionne['qte']);
                $paletteProduit->setPalette($palette);
                $entityManager->persist($paletteProduit);
            }
            
            $produitsSelectionnes = array();

            $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findAll();
            foreach ($stockProduitsReceptionnes as $stockProduitsReceptionne)
            if ( $stockProduitsReceptionne->getQuantite() === 0 ) {
                $entityManager->remove($stockProduitsReceptionne);
            }

            $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findAll();
            foreach ($stockProduitsLibres as $stockProduitsLibre)
            if ( $stockProduitsLibre->getQuantite() === 0 ) {
                $entityManager->remove($stockProduitsLibre);
            }

            $entityManager->flush();
            $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findAll();
            $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findAll();
        }

        $request->getSession()->set('produits_selectionnes', $produitsSelectionnes);

        return $this->render('creation_palette/index.html.twig', [
            'controller_name' => 'CreationPaletteController',
            'stockProduitsReceptionnes' => $stockProduitsReceptionnes,
            'stockProduitsLibres' => $stockProduitsLibres,
            'produitsSelectionnes' => $produitsSelectionnes
        ]);
    }
}
