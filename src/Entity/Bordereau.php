<?php

namespace App\Entity;

use App\Repository\BordereauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BordereauRepository::class)]
class Bordereau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 400)]
    private ?string $num_bordereau = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_reception = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo_2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\OneToMany(mappedBy: 'bordereau', targetEntity: Retour::class)]
    private Collection $retours;

    public function __construct()
    {
        $this->retours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumBordereau(): ?string
    {
        return $this->num_bordereau;
    }

    public function setNumBordereau(string $num_bordereau): static
    {
        $this->num_bordereau = $num_bordereau;

        return $this;
    }

    public function getDateReception(): ?\DateTimeInterface
    {
        return $this->date_reception;
    }

    public function setDateReception(\DateTimeInterface $date_reception): static
    {
        $this->date_reception = $date_reception;

        return $this;
    }

    public function getPhoto1(): ?string
    {
        return $this->photo_1;
    }

    public function setPhoto1(string $photo_1): static
    {
        $this->photo_1 = $photo_1;

        return $this;
    }

    public function getPhoto2(): ?string
    {
        return $this->photo_2;
    }

    public function setPhoto2(?string $photo_2): static
    {
        $this->photo_2 = $photo_2;

        return $this;
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

    /**
     * @return Collection<int, Retour>
     */
    public function getRetours(): Collection
    {
        return $this->retours;
    }

    public function addRetour(Retour $retour): static
    {
        if (!$this->retours->contains($retour)) {
            $this->retours->add($retour);
            $retour->setBordereau($this);
        }

        return $this;
    }

    public function removeRetour(Retour $retour): static
    {
        if ($this->retours->removeElement($retour)) {
            // set the owning side to null (unless already changed)
            if ($retour->getBordereau() === $this) {
                $retour->setBordereau(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->num_bordereau;
    }

}