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

class FormulaireLitigeController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/formulaire/litige/{id}', name: 'app_formulaire_litige')]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $palettes = $entityManager->getRepository(Palette::class)->findAll();
        $retour = $entityManager->getRepository(Retour::class)->find($id);
        $numretour = $retour->getNumRetour();
        $transporteur = $retour->getTransporteur();
        $etatRetour = $retour->getEtat();
        $etatProduitRetour = $retour->getEtatProduit();
        $commentaireRetour = $retour->getCommentaire();
        $photoRetour1 = $retour->getPhoto1();
        $photoRetour2 = $retour->getPhoto2();
        $photoRetour3 = $retour->getPhoto3();
        $photoRetour4 = $retour->getPhoto4();
        $photoRetour5 = $retour->getPhoto5();
        $retourProduits = array_merge($retour->getRetourProduits()->toArray());

        if ($request->isMethod('POST')) {

            $photo1 = $request->files->get('photo1');
            $photo2 = $request->files->get('photo2');
            $photo3 = $request->files->get('photo3');
            $photo4 = $request->files->get('photo4');
            $photo5 = $request->files->get('photo5');

            if ($photo1) {
                $fileName = 'litige_' . $numretour . '_photo1.jpeg';
                $photoPath1 = '/litiges/photos/' . $fileName;
                $photo1->move($this->getParameter('kernel.project_dir') . '/public/litiges/photos/', $fileName);
            }

            if ($photo2) {
                $fileName = 'litige_' . $retour->getNumRetour() . '_photo2.jpeg';
                $photoPath2 = '/litiges/photos/' . $fileName;
                $photo2->move($this->getParameter('kernel.project_dir') . '/public/litiges/photos/', $fileName);
            }

            if ($photo3) {
                $fileName = 'litige_' . $retour->getNumRetour() . '_photo3.jpeg';
                $photoPath3 = '/litiges/photos/' . $fileName;
                $photo3->move($this->getParameter('kernel.project_dir') . '/public/litiges/photos/', $fileName);
            }

            if ($photo4) {
                $fileName = 'litige_' . $retour->getNumRetour() . '_photo4.jpeg';
                $photoPath4 = '/litiges/photos/' . $fileName;
                $photo4->move($this->getParameter('kernel.project_dir') . '/public/litiges/photos/', $fileName);
            }

            if ($photo5) {
                $fileName = 'litige_' . $retour->getNumRetour() . '_photo5.jpeg';
                $photoPath5 = '/litiges/photos/' . $fileName;
                $photo5->move($this->getParameter('kernel.project_dir') . '/public/litiges/photos/', $fileName);
            }

            $retourObj = $entityManager->getRepository(Retour::class)->find($retour->getId());

            $transporteur = $request->request->get('transporteur-form-litige');
            $etat = $request->request->get('etat-form-litige');
            $etatProduit = $request->request->get('etat-produit-form-litige');
            $commentaire = $request->request->get('commentaire-form-litige');

            $retourTraite = $entityManager->getRepository(Retour::class)->find($id);
            $retourTraite->setTransporteur($transporteur);
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
            $retourTraite->setEtat($etat);
            $retourTraite->setEtatProduit($etatProduit);
            if (isset($commentaire)) {
                $retourTraite->setCommentaire($commentaire);
            }
            $currentDate = new \DateTime();
            $retourTraite->setDateTraitement($currentDate);
            if (isset($photoPath1)) {
                $retourTraite->setPhoto1($photoPath1);
            }
            if (isset($photoPath2)) {
                $retourTraite->setPhoto2($photoPath2);
            }
            if (isset($photoPath3)) {
                $retourTraite->setPhoto3($photoPath3);
            }
            if (isset($photoPath4)) {
                $retourTraite->setPhoto4($photoPath4);
            }
            if (isset($photoPath5)) {
                $retourTraite->setPhoto5($photoPath5);
            }
            $entityManager->persist($retourTraite);

            foreach ($retourProduits as $retourProduit) {

                $idProduitReceptionne = $request->request->get('id-form-litige_' . $retourProduit->getId());

                for ($p = $retourProduit->getQuantite(); $p >= 1; $p--) {

                    if (
                        // $request->request->get('code-couleur-form-litige_' . $p) !== 'pas-de-produit'
                        $paletteId = $request->request->get('form-litige-palette_' . $idProduitReceptionne . '_' . $p) !== 'pas-de-produit'
                    ) {
                        // $codeCouleur = $request->request->get('code-couleur-form-litige_' . $p);
                        $paletteProduit = new PaletteProduit();
                        $paletteId = $request->request->get('form-litige-palette_' . $idProduitReceptionne . '_' . $p);
                        $palette = $entityManager->getRepository(Palette::class)->find($paletteId);
                        $codeCouleur = $palette->getCodeCouleur();
                        $paletteProduit->setPalette($palette);
                        $paletteProduit->setIdProduit($idProduitReceptionne);
                        $paletteProduit->setQuantite(1);
                        $paletteProduit->setCodeCouleur($codeCouleur);
                        $paletteProduit->setDateReception($currentDate);
                        $entityManager->persist($paletteProduit);
                        $retourProduit = new RetourProduitReceptionnes();
                        $retourProduit->setIdProduit($idProduitReceptionne);
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

            $idProduits = $request->request->all('id-form-litige', []);
            // $codeCouleurs = $request->request->all('code-couleur-form-litige', []);
            $quantites = $request->request->all('quantite-form-litige', []);
            $idPaletteProduitReceptionne = $request->request->all('form-litige-palette', []);

            foreach ($idProduits as $index => $idProduit) {
                $paletteProduitReceptionne = new PaletteProduit();
                $palette = $entityManager->getRepository(Palette::class)->find($idPaletteProduitReceptionne[$index]);
                $codeCouleur = $palette->getCodeCouleur();
                $paletteProduitReceptionne->setPalette($palette);
                $paletteProduitReceptionne->setIdProduit($idProduit);
                $paletteProduitReceptionne->setQuantite($quantites[$index]);
                $paletteProduitReceptionne->setDateReception($currentDate);
                $paletteProduitReceptionne->setCodeCouleur($codeCouleur);
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

            $idStockProduits = $request->request->all('id-form-litige', []);
            // $codeCouleursStock = $request->request->all('code-couleur-form-litige', []);
            $quantitesStock = $request->request->all('quantite-form-litige', []);
            $idPaletteProduitStock = $request->request->all('form-litige-palette', []);

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

        $csrfTokenLitige = $this->csrfTokenManager->getToken('form-litige');

        return $this->render('formulaire_litige/index.html.twig', [
            'controller_name' => 'FormulaireLitigeController',
            'numretour' => $numretour,
            'transporteur' => $transporteur,
            'photo1' => $photoRetour1,
            'photo2' => $photoRetour2,
            'photo3' => $photoRetour3,
            'photo4' => $photoRetour4,
            'photo5' => $photoRetour5,
            'etat' => $etatRetour,
            'etatProduit' => $etatProduitRetour,
            'commentaire' => $commentaireRetour,
            'retourProduits' => $retourProduits,
            'id' => $id,
            'palettes' => $palettes
        ]);
    }
}
