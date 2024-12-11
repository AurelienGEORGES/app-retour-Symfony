<?php
namespace App\Tests\Entity;

use App\Entity\Palette;
class PaletteTest extends \PHPUnit\Framework\TestCase
{
    public function testPalette()
    {
        $code_couleur = 'rouge';
        $depot = 'abérial';
        $date_termine = new \DateTimeImmutable('2024-08-23T09:32:20+00:00');
        $date_transmise = new \DateTimeImmutable('2024-08-24T09:32:20+00:00');
        $statut = 'terminée'; 
        $palette = new Palette();
        $palette->setCodeCouleur($code_couleur);
        $palette->setDepot($depot);
        $palette->setDateTermine($date_termine);
        $palette->setDateTransmise($date_transmise);
        $palette->setStatut($statut);
        $this->assertEquals($code_couleur, $palette->getCodeCouleur());
        $this->assertEquals($depot, $palette->getDepot());
        $this->assertEquals($date_termine, $palette->getDateTermine());
        $this->assertEquals($date_transmise, $palette->getDateTransmise());
        $this->assertEquals($statut, $palette->getStatut());
    }
}