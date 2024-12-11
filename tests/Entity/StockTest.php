<?php
namespace App\Tests\Entity;

use App\Entity\Stock;
class StockTest extends \PHPUnit\Framework\TestCase
{
    public function testStock()
    {
        $id_produit = 220041;
        $quantite = 1;
        $code_couleur = 'jaune';
        $date_reception = new \DateTimeImmutable('2024-08-23T09:32:20+00:00'); 
        $stock = new Stock();
        $stock->setIdProduit($id_produit);
        $stock->setQuantite($quantite);
        $stock->setCodeCouleur($code_couleur);
        $stock->setDateReception($date_reception);
        $this->assertEquals($code_couleur, $stock->getCodeCouleur());
        $this->assertEquals($quantite, $stock->getQuantite());
        $this->assertEquals($code_couleur, $stock->getCodeCouleur());
        $this->assertEquals($date_reception, $stock->getDateReception());
    }
}