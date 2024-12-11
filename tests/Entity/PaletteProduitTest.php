<?php
namespace App\Tests\Entity;

use App\Entity\Palette;
use App\Entity\PaletteProduit;
class PaletteProduitTest extends \PHPUnit\Framework\TestCase
{
    public function testPaletteProduit()
    {

        $palette = new Palette();
        $id_produit = 210043;
        $quantite = 1;
        $date_reception = new \DateTimeImmutable('2024-08-24T09:32:20+00:00');
        $code_couleur = 'vert';
        $statut = 'archive'; 
        $paletteProduit = new PaletteProduit();
        $paletteProduit->setPalette($palette);
        $paletteProduit->setIdProduit($id_produit);
        $paletteProduit->setQuantite($quantite);
        $paletteProduit->setDateReception($date_reception);
        $paletteProduit->setCodeCouleur($code_couleur);
        $paletteProduit->setStatut($statut);
        $this->assertEquals($palette, $paletteProduit->getPalette());
        $this->assertEquals($id_produit, $paletteProduit->getIdProduit());
        $this->assertEquals($quantite, $paletteProduit->getQuantite());
        $this->assertEquals($date_reception, $paletteProduit->getDateReception());
        $this->assertEquals($code_couleur, $paletteProduit->getCodeCouleur());
        $this->assertEquals($statut, $paletteProduit->getStatut());
    }
}