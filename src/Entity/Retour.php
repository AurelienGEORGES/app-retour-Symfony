<?php

namespace App\Entity;

use App\Repository\RetourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RetourRepository::class)]
class Retour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $num_retour = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_autorisation = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $nom_client = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $prenom_client = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $transporteur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_traitement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $photo_1 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $photo_2 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $photo_3 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $photo_4 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $photo_5 = null;

    #[ORM\OneToMany(mappedBy: 'retour', targetEntity: RetourProduit::class)]
    private Collection $retourProduits;

    #[ORM\OneToMany(mappedBy: 'retour', targetEntity: RetourProduitReceptionnes::class)]
    private Collection $retourProduitReceptionnes;

    #[ORM\ManyToOne(inversedBy: 'retours')]
    private ?Bordereau $bordereau = null;

    public function __construct()
    {
        $this->retourProduits = new ArrayCollection();
        $this->retourProduitReceptionnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumRetour(): ?string
    {
        return $this->num_retour;
    }

    public function setNumRetour(?string $num_retour): static
    {
        $this->num_retour = $num_retour;

        return $this;
    }

    public function getDateAutorisation(): ?\DateTimeInterface
    {
        return $this->date_autorisation;
    }

    public function setDateAutorisation(?\DateTimeInterface $date_autorisation): static
    {
        $this->date_autorisation = $date_autorisation;

        return $this;
    }

    public function getNomClient(): ?string
    {
        return $this->nom_client;
    }

    public function setNomClient(?string $nom_client): static
    {
        $this->nom_client = $nom_client;

        return $this;
    }

    public function getPrenomClient(): ?string
    {
        return $this->prenom_client;
    }

    public function setPrenomClient(?string $prenom_client): static
    {
        $this->prenom_client = $prenom_client;

        return $this;
    }

    public function getTransporteur(): ?string
    {
        return $this->transporteur;
    }

    public function setTransporteur(?string $transporteur): static
    {
        $this->transporteur = $transporteur;

        return $this;
    }

    public function getDateTraitement(): ?\DateTimeInterface
    {
        return $this->date_traitement;
    }

    public function setDateTraitement(?\DateTimeInterface $date_traitement): static
    {
        $this->date_traitement = $date_traitement;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

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

    public function getPhoto1(): ?string
    {
        return $this->photo_1;
    }

    public function setPhoto1(?string $photo_1): static
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

    public function getPhoto3(): ?string
    {
        return $this->photo_3;
    }

    public function setPhoto3(?string $photo_3): static
    {
        $this->photo_3 = $photo_3;

        return $this;
    }

    public function getPhoto4(): ?string
    {
        return $this->photo_4;
    }

    public function setPhoto4(?string $photo_4): static
    {
        $this->photo_4 = $photo_4;

        return $this;
    }

    public function getPhoto5(): ?string
    {
        return $this->photo_5;
    }

    public function setPhoto5(?string $photo_5): static
    {
        $this->photo_5 = $photo_5;

        return $this;
    }

    /**
     * @return Collection<int, RetourProduit>
     */
    public function getRetourProduits(): Collection
    {
        return $this->retourProduits;
    }

    public function addRetourProduit(RetourProduit $retourProduit): static
    {
        if (!$this->retourProduits->contains($retourProduit)) {
            $this->retourProduits->add($retourProduit);
            $retourProduit->setIdRetour($this);
        }

        return $this;
    }

    public function removeRetourProduit(RetourProduit $retourProduit): static
    {
        if ($this->retourProduits->removeElement($retourProduit)) {
            // set the owning side to null (unless already changed)
            if ($retourProduit->getIdRetour() === $this) {
                $retourProduit->setIdRetour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RetourProduitReceptionnes>
     */
    public function getRetourProduitReceptionnes(): Collection
    {
        return $this->retourProduitReceptionnes;
    }

    public function addRetourProduitReceptionne(RetourProduitReceptionnes $retourProduitReceptionne): static
    {
        if (!$this->retourProduitReceptionnes->contains($retourProduitReceptionne)) {
            $this->retourProduitReceptionnes->add($retourProduitReceptionne);
            $retourProduitReceptionne->setRetour($this);
        }

        return $this;
    }

    public function removeRetourProduitReceptionne(RetourProduitReceptionnes $retourProduitReceptionne): static
    {
        if ($this->retourProduitReceptionnes->removeElement($retourProduitReceptionne)) {
            // set the owning side to null (unless already changed)
            if ($retourProduitReceptionne->getRetour() === $this) {
                $retourProduitReceptionne->setRetour(null);
            }
        }

        return $this;
    }

    public function getBordereau(): ?Bordereau
    {
        return $this->bordereau;
    }

    public function setBordereau(?Bordereau $bordereau): static
    {
        $this->bordereau = $bordereau;

        return $this;
    }
}
