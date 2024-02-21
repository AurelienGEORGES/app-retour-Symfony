<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\ProduitLibre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AjouterProduitLibreController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/produit', name: 'app_ajouter_produit_libre')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {

        if ($request->isMethod('POST')) {

                $idProduitLibre = $request->request->get('form-produit-libre-id');
                $codeCouleur = $request->request->get('form-produit-libre-code-couleur');
                $quantite = $request->request->get('form-produit-libre-quantite');
                $transporteur = $request->request->get('form-produit-libre-transporteur');
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
                $entityManager->persist($stock);
            }      
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Le produit libre a bien été enregistré!'
            );

        return $this->render('ajouter_produit_libre/index.html.twig', [
            'controller_name' => 'AjouterProduitLibreController',
        ]);
    }
}
