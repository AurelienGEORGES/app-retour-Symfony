<?php

namespace App\Controller;

use App\Entity\Retour;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormulaireSansLitigeController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/formulaire/sans/litige/{id}', name: 'app_formulaire_sans_litige')]
    public function index($id, EntityManagerInterface $entityManager, Request $request): Response
    {
        
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
            $retourTraite->setNumretour($numRetourTraite.'-01');
            $entityManager->persist($retourTraite);

            foreach ($retourProduits as $retourProduit) {
                $idProduitReceptionnes = $request->request->get('id-form-sans-litige_' . $retourProduit->getId());
                $codeCouleur = $request->request->get('code-couleur-form-sans-litige_' . $retourProduit->getId());
                $quantite = $request->request->get('quantite-form-sans-litige_' . $retourProduit->getId());
                $retourProduit = new RetourProduitReceptionnes();
                $retourProduit->setIdproduit($idProduitReceptionnes);
                $retourProduit->setCodeCouleur($codeCouleur);
                $retourProduit->setQuantite($quantite);
                $retourProduit->setRetour($retourObj);
                $entityManager->persist($retourProduit);
            }

            $idProduits = $request->request->all('id-form-sans-litige', []);
            $codeCouleurs = $request->request->all('code-couleur-form-sans-litige', []);
            $quantites = $request->request->all('quantite-form-sans-litige', []);

            foreach ($idProduits as $index => $idProduit) {
                $produit = new RetourProduitReceptionnes();
                $produit->setIdproduit($idProduit);
                $produit->setCodeCouleur($codeCouleurs[$index]);
                $produit->setQuantite($quantites[$index]);
                $produit->setRetour($retourObj);
                $entityManager->persist($produit);
            }

            $entityManager->flush();

        }

        return $this->render('formulaire_sans_litige/index.html.twig', [
            'controller_name' => 'FormulaireSansLitigeController',
            'transporteur' => $transporteur,
            'numretour' => $numretour,
            'retourProduits' => $retourProduits,
            'id' => $id
        ]);
    }
}
