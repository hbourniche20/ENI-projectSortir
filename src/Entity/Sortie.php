<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSortie;

    /**
     * @ORM\Column(type="date")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPlaces;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="sortiesOrganisee")
     * @ORM\JoinColumn(nullable=false)
     */
    private $villeOrganisatrice;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $villeAccueil;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="sorties")
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sortiesOrga")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToMany(targetEntity=user::class, inversedBy="sorties")
     */
    private $inscrits;

    public function __construct(User $user)
    {
        date_default_timezone_set('Europe/Paris');
        $this->inscrits = new ArrayCollection();
        $this->dateSortie = new \DateTime(date('Y/m/d H:i'));
        $this->dateLimiteInscription = new \DateTime(date('Y/m/d'));
        $this->organisateur = $user;
        $this->villeOrganisatrice = $user->getVille();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): self
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getVilleOrganisatrice(): ?Ville
    {
        return $this->villeOrganisatrice;
    }

    public function setVilleOrganisatrice(?Ville $villeOrganisatrice): self
    {
        $this->villeOrganisatrice = $villeOrganisatrice;

        return $this;
    }

    public function getVilleAccueil(): ?Ville
    {
        return $this->villeAccueil;
    }

    public function setVilleAccueil(?Ville $ville): self
    {
        $this->villeAccueil = $ville;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getInscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addInscrit(user $inscrit): self
    {
        if (!$this->inscrits->contains($inscrit)) {
            $this->inscrits[] = $inscrit;
        }

        return $this;
    }

    public function removeInscrit(user $inscrit): self
    {
        $this->inscrits->removeElement($inscrit);

        return $this;
    }

    public function getNbInscrits(): ?int
    {
     return $this->getInscrits()->count();
    }
}
