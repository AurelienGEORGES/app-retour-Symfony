<?php

namespace App\Entity;

use App\Repository\CamionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CamionRepository::class)]
class Camion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(['max' => 500])]
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $commentaire = null;

    #[Assert\Choice(['vert', 'jaune', 'orange', 'rouge', 'noir', 'SAV'])]
    #[Assert\Length(['max' => 20])]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateC = null;

    #[Assert\Length(['max' => 30])]
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateE = null;

    #[Assert\Length(['max' => 30])]
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $depot = null;

    /**
     * @var Collection<int, CamionPalette>
     */
    #[ORM\OneToMany(mappedBy: 'camion', targetEntity: CamionPalette::class)]
    private Collection $camionPalettes;

    public function __construct()
    {
        $this->camionPalettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->dateC;
    }

    public function setDateC(?\DateTimeInterface $dateC): static
    {
        $this->dateC = $dateC;

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

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->dateE;
    }

    public function setDateE(?\DateTimeInterface $dateE): static
    {
        $this->dateE = $dateE;

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
     * @return Collection<int, CamionPalette>
     */
    public function getCamionPalettes(): Collection
    {
        return $this->camionPalettes;
    }

    public function addCamionPalette(CamionPalette $camionPalette): static
    {
        if (!$this->camionPalettes->contains($camionPalette)) {
            $this->camionPalettes->add($camionPalette);
            $camionPalette->setCamion($this);
        }

        return $this;
    }

    public function removeCamionPalette(CamionPalette $camionPalette): static
    {
        if ($this->camionPalettes->removeElement($camionPalette)) {
            // set the owning side to null (unless already changed)
            if ($camionPalette->getCamion() === $this) {
                $camionPalette->setCamion(null);
            }
        }

        return $this;
    }
}
