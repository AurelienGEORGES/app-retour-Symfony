<?php

namespace App\Entity;

use App\Repository\RetourProduitReceptionnesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourProduitReceptionnesRepository::class)]
class RetourProduitReceptionnes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'retourProduitReceptionnes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Retour $retour = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id_produit = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $quantite = null;

    #[ORM\Column(length: 10)]
    private ?string $code_couleur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRetour(): ?Retour
    {
        return $this->retour;
    }

    public function setRetour(?Retour $retour): static
    {
        $this->retour = $retour;

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

    public function getCodeCouleur(): ?string
    {
        return $this->code_couleur;
    }

    public function setCodeCouleur(string $code_couleur): static
    {
        $this->code_couleur = $code_couleur;

        return $this;
    }
}
