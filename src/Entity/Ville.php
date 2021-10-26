<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
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
     * @ORM\Column(type="integer")
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Site::class, mappedBy="ville", orphanRemoval=true)
     */
    private $sites;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="villeOrganisatrice", orphanRemoval=true)
     */
    private $sortiesOrganisees;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="$villeAccueil", orphanRemoval=true)
     */
    private $sorties;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
        $this->sorties = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function __toString(): string {
        return $this->getNom() . ' (' . $this->getCodePostal() . ')';
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

    public function getCodePostal(): ?int
    {
        return $this->codePostal;
    }

    public function setCodePostal(int $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection|Site[]
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): self
    {
        if (!$this->sites->contains($site)) {
            $this->sites[] = $site;
            $site->setVille($this);
        }

        return $this;
    }

    public function removeSite(Site $site): self
    {
        if ($this->sites->removeElement($site)) {
            // set the owning side to null (unless already changed)
            if ($site->getVille() === $this) {
                $site->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addsortiesOrganisees(Sortie $sortie): self
    {
        if (!$this->sortiesOrganisees->contains($sortie)) {
            $this->sortiesOrganisees[] = $sortie;
            $sortie->setVilleOrganisatrice($this);
        }

        return $this;
    }

    public function removeSortiesOrganisees(Sortie $sortie): self
    {
        if ($this->sortiesOrganisees->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getVilleOrganisatrice() === $this) {
                $sortie->setVilleOrganisatrice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addsortie(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->setVilleAccueil($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getVilleAccueil() === $this) {
                $sortie->setVilleAccueil(null);
            }
        }

        return $this;
    }
}
