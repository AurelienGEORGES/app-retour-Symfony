<?php

namespace App\Controller;

use App\Entity\ProduitLibre;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RetourProduitReceptionnes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ListeStockController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/liste/stock', name: 'app_liste_stock')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $stockProduitsReceptionnes = $entityManager->getRepository(RetourProduitReceptionnes::class)->findAll();
        $stockProduitsLibres = $entityManager->getRepository(ProduitLibre::class)->findAll();

        if (!empty($request)) {

            $idProduitStock = $request->query->get('recherche-stock-id-produit');
            $couleurProduitStock = $request->query->get('recherche-stock-code-couleur');

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

        return $this->render('liste_stock/index.html.twig', [
            'controller_name' => 'ListeStockController',
            'stockProduitsReceptionnes' => $stockProduitsReceptionnes,
            'stockProduitsLibres' => $stockProduitsLibres
        ]);
    }
}
