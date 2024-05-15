<?php

namespace App\Entity;

use App\Repository\PaletteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaletteRepository::class)]
class Palette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code_couleur = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $depot = null;

    #[ORM\OneToMany(mappedBy: 'palette', targetEntity: PaletteProduit::class)]
    private Collection $paletteProduits;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_termine = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_transmise = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $statut = null;

    public function __construct()
    {
        $this->paletteProduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCouleur(): ?string
    {
        return $this->code_couleur;
    }

    public function setCodeCouleur(?string $code_couleur): static
    {
        $this->code_couleur = $code_couleur;

        return $this;
    }

    public function getDepot(): ?string
    {
        return $this->depot;
    }

    public function setDepot(?string $depot): static
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * @return Collection<int, PaletteProduit>
     */
    public function getPaletteProduits(): Collection
    {
        return $this->paletteProduits;
    }

    public function addPaletteProduit(PaletteProduit $paletteProduit): static
    {
        if (!$this->paletteProduits->contains($paletteProduit)) {
            $this->paletteProduits->add($paletteProduit);
            $paletteProduit->setPalette($this);
        }

        return $this;
    }

    public function removePaletteProduit(PaletteProduit $paletteProduit): static
    {
        if ($this->paletteProduits->removeElement($paletteProduit)) {
            // set the owning side to null (unless already changed)
            if ($paletteProduit->getPalette() === $this) {
                $paletteProduit->setPalette(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getDateTermine(): ?\DateTimeInterface
    {
        return $this->date_termine;
    }

    public function setDateTermine(?\DateTimeInterface $date_termine): static
    {
        $this->date_termine = $date_termine;

        return $this;
    }

    public function getDateTransmise(): ?\DateTimeInterface
    {
        return $this->date_transmise;
    }

    public function setDateTransmise(?\DateTimeInterface $date_transmise): static
    {
        $this->date_transmise = $date_transmise;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
