<?php

namespace App\Entity;

use App\Repository\PaletteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaletteRepository::class)]
class Palette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Choice(['vert', 'jaune', 'orange', 'rouge', 'noir', 'SAV'])]
    #[Assert\Length(['max' => 10])]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code_couleur = null;

    #[Assert\Choice(['abérial', 'philéa', 'SAV', 'sans dépot', 'soldeur', 'Emmaus'])]
    #[Assert\Length(['max' => 15])]
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $depot = null;

    #[ORM\OneToMany(mappedBy: 'palette', targetEntity: PaletteProduit::class)]
    private Collection $paletteProduits;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_termine = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_transmise = null;

    #[Assert\Choice(['en cours', 'terminée', 'transmise', 'camion'])]
    #[Assert\Length(['max' => 20])]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $statut = null;

    #[ORM\OneToOne(mappedBy: 'palette', cascade: ['persist', 'remove'])]
    private ?CamionPalette $camionPalette = null;

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

    public function getCamionPalette(): ?CamionPalette
    {
        return $this->camionPalette;
    }

    public function setCamionPalette(CamionPalette $camionPalette): static
    {
        // set the owning side of the relation if necessary
        if ($camionPalette->getPalette() !== $this) {
            $camionPalette->setPalette($this);
        }

        $this->camionPalette = $camionPalette;

        return $this;
    }
}
