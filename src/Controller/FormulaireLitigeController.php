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
    #[Route('/formulaire/litige/{id}', name: 'app_formulaire_litige', methods: ['GET', 'POST'])]
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
        $etatRetour = $retour->getEtat();
        $etatProduitRetour = $retour->getEtatProduit();
        $commentaireRetour = $retour->getCommentaire();
        $photoRetour1 = $retour->getPhoto1();
        $photoIdProduit1 = $retour->getIdProduitPhoto1();
        $photoRetour2 = $retour->getPhoto2();
        $photoIdProduit2 = $retour->getIdProduitPhoto2();
        $photoRetour3 = $retour->getPhoto3();
        $photoIdProduit3 = $retour->getIdProduitPhoto3();
        $photoRetour4 = $retour->getPhoto4();
        $photoIdProduit4 = $retour->getIdProduitPhoto4();
        $photoRetour5 = $retour->getPhoto5();
        $photoIdProduit5 = $retour->getIdProduitPhoto5();

        $retourProduits = array_merge($retour->getRetourProduits()->toArray());
        $retourProduitsDejaReceptionnes = array_merge($retour->getRetourProduitReceptionnes()->toArray());

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

            if (!empty($request->request->get('produit-photo-id-1'))) {
                $retourTraite->setIdProduitPhoto1($request->request->get('produit-photo-id-1'));
            }
            if (!empty($request->request->get('produit-photo-id-2'))) {
                $retourTraite->setIdProduitPhoto2($request->request->get('produit-photo-id-2'));
            }
            if (!empty($request->request->get('produit-photo-id-3'))) {
                $retourTraite->setIdProduitPhoto3($request->request->get('produit-photo-id-3'));
            }
            if (!empty($request->request->get('produit-photo-id-4'))) {
                $retourTraite->setIdProduitPhoto4($request->request->get('produit-photo-id-4'));
            }
            if (!empty($request->request->get('produit-photo-id-5'))) {
                $retourTraite->setIdProduitPhoto5($request->request->get('produit-photo-id-5'));
            }

            $currentDate = new \DateTime();

            $numRetourTraite = $retourTraite->getNumRetour();
            if (substr($numRetourTraite, -3) === '-01') {
                $numRetourTraite = substr_replace($numRetourTraite, '-02', -3);
                $retourTraite->setEtat02($etat);
                $retourTraite->setEtatProduit02($etatProduit);
                $retourTraite->setDateTraitement02($currentDate);
            } else if (substr($numRetourTraite, -3) === '-02') {
                $numRetourTraite = substr_replace($numRetourTraite, '-03', -3);
                $retourTraite->setEtat03($etat);
                $retourTraite->setEtatProduit03($etatProduit);
                $retourTraite->setDateTraitement03($currentDate);
            } else if (substr($numRetourTraite, -3) === '-03') {
                $numRetourTraite = substr_replace($numRetourTraite, '-04', -3);
            } else {
                $numRetourTraite .= '-01';
                $retourTraite->setEtat($etat);
                $retourTraite->setEtatProduit($etatProduit);
                $retourTraite->setDateTraitement($currentDate);
            }
            $retourTraite->setNumretour($numRetourTraite);

            if (isset($commentaire)) {
                $retourTraite->setCommentaire($commentaire);
            }

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
            $entityManager->flush();

            foreach ($retourProduitsAReceptionner as $retourProduit) {

                $idProduitReceptionne = $request->request->get('id-form-litige_' . $retourProduit['id']);

                for ($p = $retourProduit['quantite']; $p >= 1; $p--) {
                    if (
                        $request->request->get('form-litige-palette_' . $idProduitReceptionne . '_' . $p) !== 'pas-de-produit' &&
                        !empty($request->request->get('form-litige-palette_' . $idProduitReceptionne . '_' . $p))
                    ) {
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
            'photoIdProduit1' => $photoIdProduit1,
            'photo2' => $photoRetour2,
            'photoIdProduit2' => $photoIdProduit2,
            'photo3' => $photoRetour3,
            'photoIdProduit3' => $photoIdProduit3,
            'photo4' => $photoRetour4,
            'photoIdProduit4' => $photoIdProduit4,
            'photo5' => $photoRetour5,
            'photoIdProduit5' => $photoIdProduit5,
            'etat' => $etatRetour,
            'etatProduit' => $etatProduitRetour,
            'commentaire' => $commentaireRetour,
            'retourProduits' => $retourProduitsAReceptionner,
            'id' => $id,
            'palettes' => $PalettesForSelect
        ]);
    }
}
