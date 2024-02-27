<?php

namespace App\Entity;

use App\Entity\Retour;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\RetourProduitRepository;

#[ORM\Entity(repositoryClass: RetourProduitRepository::class)]
class RetourProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id_produit = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'retourProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Retour $retour = null;

    public function getId(): ?int
    {
        return $this->id;
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

    // public function getIdRetour(): ?Retour
    // {
    //     return $this->retour;
    // }

    // public function setIdRetour(?Retour $retour): static
    // {
    //     $this->retour = $retour;

    //     return $this;
    // }

    public function getRetour(): ?Retour
    {
        return $this->retour;
    }

    public function setRetour(?Retour $retour): static
    {
        $this->retour = $retour;

        return $this;
    }

    public function __toString()
    {
        return $this->retour;
    }
}
