<?php

namespace App\Controller;

use App\Entity\Stock;
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

        $stockProduits = $entityManager->getRepository(Stock::class)->findAll();
        
        if (!empty($request)) {

            $idProduitStock = $request->query->get('recherche-stock-id-produit');
            $couleurProduitStock = $request->query->get('recherche-stock-code-couleur');

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

        return $this->render('liste_stock/index.html.twig', [
            'controller_name' => 'ListeStockController',
            'stockProduits' => $stockProduits
        ]);
    }
}
