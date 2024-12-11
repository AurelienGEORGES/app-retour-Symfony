<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\Retour;
use App\Entity\Palette;
use App\Entity\PaletteProduit;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormulaireSansLitigeController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/formulaire/sans/litige/{id}', name: 'app_formulaire_sans_litige', methods: ['GET', 'POST'])]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {

        $allpalettesForSelect = $entityManager->getRepository(Palette::class)->findAll();
        $PalettesForSelect = [];
        foreach ($allpalettesForSelect as $Palette) {
            
            if ($Palette->getStatut() !== 'transmise' && $Palette->getStatut() !== 'camion' && $Palette->getStatut() !== 'terminée') {
                $PalettesForSelect[] = $Palette;
            }
        }
        $retour = $entityManager->getRepository(Retour::class)->find($id);
        $numretour = $retour->getNumRetour();
        $transporteur = $retour->getTransporteur();
        $retourProduits = array_merge($retour->getRetourProduits()->toArray());
        $retourProduitsDejaReceptionnes = array_merge($retour->getRetourProduitReceptionnes()->toArray());
        $retourObj = $entityManager->getRepository(Retour::class)->find($retour->getId());

        $retourProduitsFormatted = [];
        foreach ($retourProduits as $produit) {
            $idProduit = $produit->getIdProduit();
            $quantite = $produit->getQuantite();
            
            if (isset($retourProduitsFormatted[$idProduit])) {
                $retourProduitsFormatted[$idProduit] += $quantite;
            } else {
                $retourProduitsFormatted[$idProduit] = $quantite;
            }
        }

        $retourProduitsDejaReceptionnesFormatted = [];
        foreach ($retourProduitsDejaReceptionnes as $produitReceptionne) {
            $idProduit = $produitReceptionne->getIdProduit();
            $quantite = $produitReceptionne->getQuantite();
            
            if (isset($retourProduitsDejaReceptionnesFormatted[$idProduit])) {
                $retourProduitsDejaReceptionnesFormatted[$idProduit] += $quantite;
            } else {
                $retourProduitsDejaReceptionnesFormatted[$idProduit] = $quantite;
            }
        }

        $retourProduitsAReceptionner = [];
        $idCounter = 1;
        foreach ($retourProduitsFormatted as $idProduit => $quantiteTotale) {
            
            $quantiteDejaReceptionnee = isset($retourProduitsDejaReceptionnesFormatted[$idProduit]) ? $retourProduitsDejaReceptionnesFormatted[$idProduit] : 0;

            $quantiteAReceptionner = max(0, $quantiteTotale - $quantiteDejaReceptionnee);

            if ($quantiteAReceptionner > 0) {
                $retourProduitsAReceptionner[] = [
                    'id' => $idCounter++,
                    'idproduit' => $idProduit,
                    'quantite' => $quantiteAReceptionner
                ];
            }
        }

        if ($request->isMethod('POST')) {

            $retourTraite = $entityManager->getRepository(Retour::class)->find($id);
            $currentDate = new \DateTime();
            
            $numRetourTraite = $retourTraite->getNumRetour();
            if (substr($numRetourTraite, -3) === '-01') {
                $numRetourTraite = substr_replace($numRetourTraite, '-02', -3);
                $retourTraite->setDateTraitement02($currentDate);
            } else if (substr($numRetourTraite, -3) === '-02') {
                $numRetourTraite = substr_replace($numRetourTraite, '-03', -3);
                $retourTraite->setDateTraitement03($currentDate);
            } else if (substr($numRetourTraite, -3) === '-03') {
                $numRetourTraite = substr_replace($numRetourTraite, '-04', -3);
            } else {
                $numRetourTraite .= '-01';
                $retourTraite->setDateTraitement($currentDate);
            }
            $retourTraite->setNumretour($numRetourTraite);
            $entityManager->persist($retourTraite);

            foreach ($retourProduitsAReceptionner as $retourProduit) {

                $idProduitReceptionne = $request->request->get('id-form-sans-litige_' . $retourProduit['id']);

                for ($p = $retourProduit['quantite']; $p >= 1; $p--) {
                    if (
                        $request->request->get('form-sans-litige-palette_' . $idProduitReceptionne . '_' . $p) !== 'pas-de-produit' &&
                        !empty($request->request->get('form-sans-litige-palette_' . $idProduitReceptionne . '_' . $p))
                    ) {
                        
                        $paletteProduit = new PaletteProduit();
                        $paletteId = $request->request->get('form-sans-litige-palette_' . $idProduitReceptionne . '_'  . $p);
                        $palette = $entityManager->getRepository(Palette::class)->find($paletteId);
                        $codeCouleur = $palette->getCodeCouleur();
                        $paletteProduit->setPalette($palette);
                        $paletteProduit->setIdProduit($idProduitReceptionne);
                        $paletteProduit->setQuantite(1);
                        $paletteProduit->setCodeCouleur($codeCouleur);
                        $paletteProduit->setDateReception($currentDate);
                        $entityManager->persist($paletteProduit);
                        $retourProduit = new RetourProduitReceptionnes();
                        $retourProduit->setIdproduit($idProduitReceptionne);
                        $retourProduit->setCodeCouleur($codeCouleur);
                        $retourProduit->setQuantite(1);
                        $retourProduit->setRetour($retourObj);
                        $retourProduit->setDateReception($currentDate);
                        $entityManager->persist($retourProduit);
                        $stock = new Stock();
                        $stock->setIdProduit($idProduitReceptionne);
                        $stock->setCodeCouleur($codeCouleur);
                        $stock->setQuantite(1);
                        $stock->setDateReception($currentDate);
                        $entityManager->persist($stock);
                        $entityManager->flush();
                    }
                }
            }

            $idProduits = $request->request->all('id-form-sans-litige', []);
            $idPaletteProduitReceptionne = $request->request->all('form-sans-litige-palette', []);
            $quantites = $request->request->all('quantite-form-sans-litige', []);

            foreach ($idProduits as $index => $idProduit) {
                $paletteProduitReceptionne = new PaletteProduit();
                $palette = $entityManager->getRepository(Palette::class)->find($idPaletteProduitReceptionne[$index]);
                $codeCouleur = $palette->getCodeCouleur();
                $paletteProduitReceptionne->setPalette($palette);
                $paletteProduitReceptionne->setIdProduit($idProduit);
                $paletteProduitReceptionne->setQuantite($quantites[$index]);
                $paletteProduitReceptionne->setCodeCouleur($codeCouleur);
                $paletteProduitReceptionne->setDateReception($currentDate);
                $entityManager->persist($paletteProduitReceptionne);
                $produit = new RetourProduitReceptionnes();
                $produit->setIdproduit($idProduit);
                $produit->setCodeCouleur($codeCouleur);
                $produit->setQuantite($quantites[$index]);
                $produit->setRetour($retourObj);
                $produit->setDateReception($currentDate);
                $entityManager->persist($produit);
                $entityManager->flush();
            }

            $idStockProduits = $request->request->all('id-form-sans-litige', []);
            $idPaletteProduitStock = $request->request->all('form-sans-litige-palette', []);
            $quantitesStock = $request->request->all('quantite-form-sans-litige', []);

            foreach ($idStockProduits as $index => $idProduit) {
                $palette = $entityManager->getRepository(Palette::class)->find($idPaletteProduitStock[$index]);
                $codeCouleurStock = $palette->getCodeCouleur();
                $stock = new Stock();
                $stock->setIdProduit($idProduit);
                $stock->setQuantite($quantitesStock[$index]);
                $stock->setCodeCouleur($codeCouleurStock);
                $stock->setDateReception($currentDate);
                $entityManager->persist($stock);
                $entityManager->flush();
            }

            $this->addFlash(
                'notice',
                'Le formulaire a bien été enregistré!'
            );
        }

        $csrfTokenProduitSansLitige = $this->csrfTokenManager->getToken('form-sans-litige');

        return $this->render('formulaire_sans_litige/index.html.twig', [
            'controller_name' => 'FormulaireSansLitigeController',
            'transporteur' => $transporteur,
            'numretour' => $numretour,
            'retourProduits' => $retourProduitsAReceptionner,
            'id' => $id,
            'palettes' => $PalettesForSelect
        ]);
    }
}
