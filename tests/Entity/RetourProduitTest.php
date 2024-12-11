<?php
namespace App\Tests\Entity;

use App\Entity\Retour;
use App\Entity\RetourProduit;

class RetourProduitTest extends \PHPUnit\Framework\TestCase
{
    public function testRetourProduit()
    {
        $retour = new Retour();
        $id_produit = 210043;
        $quantite = 1;
        $etat = 'archive'; 
        $retourProduit = new RetourProduit();
        $retourProduit->setRetour($retour);
        $retourProduit->setIdProduit($id_produit);
        $retourProduit->setQuantite($quantite);
        $retourProduit->setEtat($etat);
        $this->assertEquals($retour, $retourProduit->getRetour());
        $this->assertEquals($id_produit, $retourProduit->getIdProduit());
        $this->assertEquals($quantite, $retourProduit->getQuantite());
        $this->assertEquals($etat, $retourProduit->getEtat());
    }
}