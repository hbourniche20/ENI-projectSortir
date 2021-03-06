<?php

    namespace App\Entity;

    use App\Repository\SiteRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity(repositoryClass=SiteRepository::class)
     */
    class Site {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="sites")
         * @ORM\JoinColumn(nullable=false)
         */
        private $ville;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $nom;

        /**
         * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="site", orphanRemoval=true)
         */
        private $sorties;

        /**
         * @ORM\Column(type="string", length=255)
         */
        private $rue;

        public function __construct() {
            $this->sorties = new ArrayCollection();
        }

        public function __toString() {
            return $this->nom;
        }

        public function getId(): ?int {
            return $this->id;
        }

        public function getVille(): ?Ville {
            return $this->ville;
        }

        public function setVille(?Ville $ville): self {
            $this->ville = $ville;
            return $this;
        }

        public function getNom(): ?string {
            return $this->nom;
        }

        public function setNom(string $nom): self {
            $this->nom = $nom;
            return $this;
        }

        /**
         * @return Collection|Sortie[]
         */
        public function getSorties(): Collection {
            return $this->sorties;
        }

        public function addSortie(Sortie $sorty): self {
            if (!$this->sorties->contains($sorty)) {
                $this->sorties[] = $sorty;
                $sorty->setSite($this);
            }
            return $this;
        }

        public function removeSortie(Sortie $sorty): self {
            // set the owning side to null (unless already changed)
            if ($this->sorties->removeElement($sorty) && $sorty->getSite() === $this) {
                $sorty->setSite(null);
            }
            return $this;
        }

        public function getRue(): ?string {
            return $this->rue;
        }

        public function setRue(string $rue): self {
            $this->rue = $rue;
            return $this;
        }
    }
