<?php
namespace App\Tests\Entity;

use App\Entity\CamionPalette;
use App\Entity\Camion;
use App\Entity\Palette;
class CamionPaletteTest extends \PHPUnit\Framework\TestCase
{
    public function testCamionPalette()
    {
        $camion = new Camion();
        $palette = new Palette();
        $camionPalette = new CamionPalette();
        $camionPalette->setCamion($camion);
        $camionPalette->setPalette($palette);
        $this->assertEquals($camion, $camionPalette->getCamion());
        $this->assertEquals($palette, $camionPalette->getPalette());
    }
}