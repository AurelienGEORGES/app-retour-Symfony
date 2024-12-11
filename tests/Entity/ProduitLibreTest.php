<?php
namespace App\Tests\Entity;

use App\Entity\ProduitLibre;
class ProduitLibreTest extends \PHPUnit\Framework\TestCase
{
    public function testProduitLibre()
    {
        $id_produit = 220133;
        $code_couleur = 'rouge';
        $quantite = 2;
        $transporteur = 'DPD';
        $produitLibre = new ProduitLibre();
        $produitLibre->setIdProduit($id_produit);
        $produitLibre->setCodeCouleur($code_couleur);
        $produitLibre->setQuantite($quantite);
        $produitLibre->setTransporteur($transporteur);
        $this->assertEquals($id_produit, $produitLibre->getIdProduit());
        $this->assertEquals($code_couleur, $produitLibre->getCodeCouleur());
        $this->assertEquals($quantite, $produitLibre->getQuantite());
        $this->assertEquals($transporteur, $produitLibre->getTransporteur());
    }
}