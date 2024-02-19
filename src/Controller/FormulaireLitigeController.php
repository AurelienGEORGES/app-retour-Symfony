<?php

namespace App\Controller;

use App\Entity\Retour;
use App\Entity\RetourProduitReceptionnes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormulaireLitigeController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/formulaire/litige/{id}', name: 'app_formulaire_litige')]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        $retour = $entityManager->getRepository(Retour::class)->find($id);
        $numretour = $retour->getNumRetour();
        $transporteur = $retour->getTransporteur();
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
            $retourTraite->setCommentaire($commentaire);
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
                if (
                    !empty($request->request->get('id-form-litige_' . $retourProduit->getId()))
                    && !empty($request->request->get('id-form-litige_' . $retourProduit->getId()))
                    && !empty($request->request->get('quantite-form-litige_' . $retourProduit->getId()))
                ) {
                    $idProduitReceptionnes = $request->request->get('id-form-litige_' . $retourProduit->getId());
                    $codeCouleur = $request->request->get('code-couleur-form-litige_' . $retourProduit->getId());
                    $quantite = $request->request->get('quantite-form-litige_' . $retourProduit->getId());
                    $retourProduit = new RetourProduitReceptionnes();
                    $retourProduit->setIdproduit($idProduitReceptionnes);
                    $retourProduit->setCodeCouleur($codeCouleur);
                    $retourProduit->setQuantite($quantite);
                    $retourProduit->setRetour($retourObj);
                    $entityManager->persist($retourProduit);
                }
            }

            $idProduits = $request->request->all('id-form-litige', []);
            $codeCouleurs = $request->request->all('code-couleur-form-litige', []);
            $quantites = $request->request->all('quantite-form-litige', []);

            foreach ($idProduits as $index => $idProduit) {
                $produit = new RetourProduitReceptionnes();
                $produit->setIdproduit($idProduit);
                $produit->setCodeCouleur($codeCouleurs[$index]);
                $produit->setQuantite($quantites[$index]);
                $produit->setRetour($retourObj);
                $entityManager->persist($produit);
            }

            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le formulaire a bien été enregistré!'
            );

        }

        return $this->render('formulaire_litige/index.html.twig', [
            'controller_name' => 'FormulaireLitigeController',
            'numretour' => $numretour,
            'transporteur' => $transporteur,
            'retourProduits' => $retourProduits,
            'id' => $id
        ]);
    }
}
