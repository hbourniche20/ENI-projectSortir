<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message="Veuillez donner un nom")
     * @Assert\Length(min="2", max="50",
     *     minMessage="Il vous faut au moins 2 caractères pour le nom de la sortie",
     *     maxMessage="Il vous faut maximum 50 caractères pour le nom de la sortie"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\Type(type="datetime", message="Veuillez entrer une date de sortie valide")
     * @Assert\GreaterThan("today")
     * @ORM\Column(type="datetime")
     */
    private \DateTime $dateSortie;

    /**
     * @Assert\Type(type="datetime", message="Veuillez entrer une date limite d'inscription valide")
     * @Assert\Expression(
     *     "this.getDateLimiteInscription() < this.getDateSortie()",
     *     message="La date fin d'inscriptions ne doit pas être supérieur à la date de début de la sortie"
     * )
     * @ORM\Column(type="date")
     */
    private \DateTime $dateLimiteInscription;

    /**
     * @Assert\Type(type="integer", message="Veuillez entrer un nombre entier pour les places disponibles")
     * @Assert\NotBlank(message="Veuillez renseigner le nombre de places disponibles")
     * @Assert\Range(min="1",
     *          minMessage="Veuillez mettre au moins une place à votre sortie")
     * @ORM\Column(type="integer")
     */
    private $nbPlaces;

    /**
     * @Assert\Type(type="integer", message="Veuillez entrer un nombre entier")
     * @Assert\NotBlank(message="Veuillez renseigner la duree de la sortie")
     * @Assert\Range(min="0",
     *    minMessage="La durée doit être un nombre positif")
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

    /**
     * @ORM\Column(type="boolean")
     */
    private $publiee;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motifAnnulation;

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

    public function setDateSortie(\DateTime $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTime $dateLimiteInscription): self
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

    public function getPubliee(): ?bool
    {
        return $this->publiee;
    }

    public function setPubliee(bool $publiee): self
    {
        $this->publiee = $publiee;

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(?string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function getDureeString() {
        $string = $this->duree.' minute';
        if ($this->duree > 1) {
            $string = $string.'s';
        }
        return $string;
    }
}
