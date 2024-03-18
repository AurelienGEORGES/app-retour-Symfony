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
    #[Route('/formulaire/sans/litige/{id}', name: 'app_formulaire_sans_litige')]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $palettes = $entityManager->getRepository(Palette::class)->findAll();
        $retour = $entityManager->getRepository(Retour::class)->find($id);
        $retourObj = $entityManager->getRepository(Retour::class)->find($retour->getId());
        $numretour = $retour->getNumRetour();
        $transporteur = $retour->getTransporteur();
        $retourProduits = array_merge($retour->getRetourProduits()->toArray());

        if ($request->isMethod('POST')) {

            $retourTraite = $entityManager->getRepository(Retour::class)->find($id);
            $currentDate = new \DateTime();
            $retourTraite->setDateTraitement($currentDate);
            $numRetourTraite = $retourTraite->getNumRetour();
            if (substr($numRetourTraite, -3) === '-01') {
                $numRetourTraite = substr_replace($numRetourTraite, '-02', -3);
            } else if (substr($numRetourTraite, -3) === '-02') {
                $numRetourTraite = substr_replace($numRetourTraite, '-03', -3);
            } else if (substr($numRetourTraite, -3) === '-03') {
                $numRetourTraite = substr_replace($numRetourTraite, '-04', -3);
            } else {
                $numRetourTraite .= '-01';
            }
            $retourTraite->setNumretour($numRetourTraite);
            $entityManager->persist($retourTraite);

            foreach ($retourProduits as $retourProduit) {

                $idProduitReceptionne = $request->request->get('id-form-sans-litige_' . $retourProduit->getId());

                for ($p = $retourProduit->getQuantite(); $p >= 1; $p--) {

                    if (
                        // $request->request->get('code-couleur-form-sans-litige_' . $p) !== 'pas-de-produit'
                        $paletteId = $request->request->get('form-sans-litige-palette_' . $idProduitReceptionne . '_' . $p) !== 'pas-de-produit'
                    ) {
                        // $codeCouleur = $request->request->get('code-couleur-form-sans-litige_' . $p);
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
            // $codeCouleurs = $request->request->all('code-couleur-form-sans-litige', []);
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
            // $codeCouleursStock = $request->request->all('code-couleur-form-sans-litige', []);
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
            'retourProduits' => $retourProduits,
            'id' => $id,
            'palettes' => $palettes
        ]);
    }
}
