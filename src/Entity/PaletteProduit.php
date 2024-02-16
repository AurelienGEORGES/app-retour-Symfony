<?php

namespace App\Entity;

use App\Repository\PaletteProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaletteProduitRepository::class)]
class PaletteProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paletteProduits')]
    private ?Palette $palette = null;

    #[ORM\Column]
    private ?int $id_produit = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPalette(): ?Palette
    {
        return $this->palette;
    }

    public function setPalette(?Palette $palette): static
    {
        $this->palette = $palette;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function setIdProduit(int $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
}
