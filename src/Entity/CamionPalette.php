<?php

namespace App\Entity;

use App\Repository\CamionPaletteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CamionPaletteRepository::class)]
class CamionPalette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'camionPalettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Camion $camion = null;

    #[ORM\OneToOne(inversedBy: 'camionPalette', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Palette $palette = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCamion(): ?Camion
    {
        return $this->camion;
    }

    public function setCamion(?Camion $camion): static
    {
        $this->camion = $camion;

        return $this;
    }

    public function getPalette(): ?Palette
    {
        return $this->palette;
    }

    public function setPalette(Palette $palette): static
    {
        $this->palette = $palette;

        return $this;
    }
}
