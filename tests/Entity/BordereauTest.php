<?php
namespace App\Tests\Entity;

use App\Entity\Bordereau;
class BordereauTest extends \PHPUnit\Framework\TestCase
{
    public function testBordereau()
    {
        $num_bordereau = 'bordereau_2024-08-26_09-32-20';
        $date_reception = new \DateTimeImmutable('2024-08-26T09:32:20+00:00');
        $photo_1 = 'bordereau_2024-08-26_09-32-20.jpeg';
        $commentaire = 'bordereau colis du matin';
        $bordereau = new Bordereau();
        $bordereau->setNumBordereau($num_bordereau);
        $bordereau->setDateReception($date_reception);
        $bordereau->setPhoto1($photo_1);
        $bordereau->setCommentaire($commentaire);
        $this->assertEquals($num_bordereau, $bordereau->getNumBordereau());
        $this->assertEquals($date_reception, $bordereau->getDateReception());
        $this->assertEquals($photo_1, $bordereau->getPhoto1());
        $this->assertEquals($commentaire, $bordereau->getCommentaire());
    }
}