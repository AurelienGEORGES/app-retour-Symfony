<?php
namespace App\Tests\Entity;

use App\Entity\Retour;
use App\Entity\RetourProduitReceptionnes;

class RetourProduitReceptionnesTest extends \PHPUnit\Framework\TestCase
{
    public function testRetourProduitReceptionnes()
    {

        $retour = new Retour();
        $id_produit = 210043;
        $quantite = 1;
        $code_couleur = 'vert';
        $date_reception = new \DateTimeImmutable('2024-08-24T09:32:20+00:00'); 
        $retourProduitReceptionnes = new RetourProduitReceptionnes();
        $retourProduitReceptionnes->setRetour($retour);
        $retourProduitReceptionnes->setIdProduit($id_produit);
        $retourProduitReceptionnes->setQuantite($quantite);
        $retourProduitReceptionnes->setCodeCouleur($code_couleur);
        $retourProduitReceptionnes->setDateReception($date_reception);
        $this->assertEquals($retour, $retourProduitReceptionnes->getRetour());
        $this->assertEquals($id_produit, $retourProduitReceptionnes->getIdProduit());
        $this->assertEquals($quantite, $retourProduitReceptionnes->getQuantite());
        $this->assertEquals($date_reception, $retourProduitReceptionnes->getDateReception());
        $this->assertEquals($code_couleur, $retourProduitReceptionnes->getCodeCouleur());
    }
}