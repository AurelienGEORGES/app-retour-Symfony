<?php
namespace App\Tests\Entity;

use App\Entity\Camion;
class CamionTest extends \PHPUnit\Framework\TestCase
{
    public function testCamion()
    {
        $commentaire = 'que des palettes pleines';
        $couleur = 'orange';
        $dateC = new \DateTimeImmutable('2024-08-23T09:32:20+00:00');
        $statut = 'cree'; 
        $dateE = new \DateTimeImmutable('2024-08-24T09:32:20+00:00');
        $depot = 'abérial';
        $camion = new Camion();
        $camion->setCommentaire($commentaire);
        $camion->setCouleur($couleur);
        $camion->setDateC($dateC);
        $camion->setStatut($statut);
        $camion->setDateE($dateE);
        $camion->setDepot($depot);
        $this->assertEquals($commentaire, $camion->getCommentaire());
        $this->assertEquals($couleur, $camion->getCouleur());
        $this->assertEquals($dateC, $camion->getDateC());
        $this->assertEquals($statut, $camion->getStatut());
        $this->assertEquals($dateE, $camion->getDateE());
        $this->assertEquals($depot, $camion->getDepot());
    }
}