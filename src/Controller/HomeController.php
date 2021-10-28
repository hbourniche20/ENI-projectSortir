<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends CustomAbstractController
{
    private $sortiesNonArchive;

    #[Route(path: '', name: 'home_page')]
    public function home(SortieRepository $sortieRepository, VilleRepository $villeRepository, Request $request): response
    {
        $userSession = $this->getUserBySession();
        $device = $userSession->isMobile();
        $sorties = $sortieRepository->findAll();
        $villes = $villeRepository->findAll();
        $this->sortiesNonArchive = array();
        $dateNow = date($this->FORMAT_DATETIME_WITH_SECONDE);
        // Archive les sorties s'il elles ont plus d'un mois
        foreach ($sorties as $sortie) {
            $date = new \DateTime($sortie->getDateSortie()->format($this->FORMAT_DATETIME_WITH_SECONDE));
            $date->add(new \DateInterval('P1M'));
            if ($date > $dateNow) {
                array_push($this->sortiesNonArchive, $sortie);
            }
        }
        $searchForm = $this->createFormBuilder()
            //Rajouter les champs du formulaire
            ->add('villeId', EntityType::class, ['class' => Ville::class, 'label' => 'Ville :', 'required' => false])
            ->add('nameSortie', TextType::class, ['required' => false, 'label' => 'Nom sortie contient :'])
            ->add('dateDebut', DateType::class, ['label' => 'Entre :', 'required' => false, 'widget' => 'single_text',])
            ->add('dateFin', DateType::class, ['label' => 'Et :', 'required' => false, 'widget' => 'single_text',])
            ->add('sortieOrganisateur', CheckboxType::class, ['label' => "Sorties dont je suis l'organisateur/trice", 'required' => false])
            ->add('sortieInscrit', CheckboxType::class, ['label' => "Sorties auxquelles je suis inscrit/e ", 'required' => false])
            ->add('sortieNonInscrit', CheckboxType::class, ['label' => "Sorties auxquelles je suis pas inscrit/e", 'required' => false])
            ->add('sortiePassees', CheckboxType::class, ['label' => "Sorties passées", 'required' => false])
            ->getForm();
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            //Modification Sortie non Archivees
            $villeId = $searchForm->get('villeId')->getData();
            $nameSortie = trim(strip_tags($searchForm->get('nameSortie')->getData()));
            $dateDebut = $searchForm->get('dateDebut')->getData();
            $dateFin = $searchForm->get('dateFin')->getData();
            $organisateur = $searchForm->get('sortieOrganisateur')->getData();
            $inscrit = $searchForm->get('sortieInscrit')->getData();
            $nonInscrit = $searchForm->get('sortieNonInscrit')->getData();
            $sortiePassees = $searchForm->get('sortiePassees')->getData();

            $this->filterByVille($villeId);
            $this->filterBySearch($nameSortie);
            $this->filterByDate($dateDebut, $dateFin);
            $this->filterOrganisateur($organisateur, $userSession);
            $this->filterInscritEtNonInscrit($inscrit, $userSession);
            $this->filterNonInscrit($nonInscrit, $userSession);
            $this->filtreSortiesPassees($sortiePassees);
        }

        if($device){
            foreach ($this->sortiesNonArchive as $sortie) {
                if ($sortie->getVilleAccueil() != $userSession->getVille() && $sortie->getVilleOrganisatrice() != $userSession->getVille()) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }

        return $this->render('homepage/home.html.twig', [
            "sorties" => $this->sortiesNonArchive,
            "sites" => $villes,
            "searchForm" => $searchForm->createView(),
            "isAdmin" => $this->isAdmin($userSession),
            "isMobile" => $this->isMobile,
            "isTablet" => $this->isTablet,
        ]);
    }

    /**
     * @param mixed $sortiePassees
     */
    private function filtreSortiesPassees(mixed $sortiePassees)
    {
        if ($sortiePassees) {
            $dateActuelle = date($this->FORMAT_DATETIME);
            foreach ($this->sortiesNonArchive as $sortie) {
                if ($sortie->getDateSortie() < $dateActuelle) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }
    }

    /**
     * @param mixed $nonInscrit
     * @param \App\Entity\User $userSession
     */
    private function filterNonInscrit(mixed $nonInscrit, \App\Entity\User $userSession)
    {
        if ($nonInscrit) {
            foreach ($this->sortiesNonArchive as $sortie) {
                foreach ($sortie->getInscrits() as $ins) {
                    if ($ins == $userSession) {
                        unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @param mixed $inscrit
     * @param \App\Entity\User $userSession
     */
    private function filterInscritEtNonInscrit(mixed $inscrit, \App\Entity\User $userSession)
    {
        if ($inscrit) {
            $estInscrit = false;
            foreach ($this->sortiesNonArchive as $sortie) {
                foreach ($sortie->getInscrits() as $ins) {
                    if ($ins == $userSession) {
                        $estInscrit = true;
                        break;
                    }
                }
                if (!$estInscrit) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
                $estInscrit = false;
            }
        }
    }

    /**
     * @param mixed $organisateur
     * @param \App\Entity\User $userSession
     */
    private function filterOrganisateur(mixed $organisateur, \App\Entity\User $userSession)
    {
        if (!empty($organisateur) && $organisateur) {
            foreach ($this->sortiesNonArchive as $sortie) {
                if ($sortie->getOrganisateur() != $userSession) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }
    }

    /**
     * @param mixed $dateDebut
     * @param mixed $dateFin
     */
    private function filterByDate(mixed $dateDebut, mixed $dateFin)
    {
        if (!empty($dateDebut) && empty($dateFin)) {

            foreach ($this->sortiesNonArchive as $sortie) {
                if (date_format($dateDebut, $this->FORMAT_DATE) > date_format($sortie->getDateSortie(), $this->FORMAT_DATE)) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        } else if (empty($dateDebut) && !empty($dateFin)) {
            foreach ($this->sortiesNonArchive as $sortie) {
                if (date_format($dateFin, $this->FORMAT_DATE) < date_format($sortie->getDateSortie(), $this->FORMAT_DATE)) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        } else if (!empty($dateDebut) && !empty($dateFin)) {
            foreach ($this->sortiesNonArchive as $sortie) {
                if (date_format($dateDebut, $this->FORMAT_DATE) > date_format($sortie->getDateSortie(), $this->FORMAT_DATE) || date_format($dateFin, $this->FORMAT_DATE) < date_format($sortie->getDateSortie(), $this->FORMAT_DATE)) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }
    }

    /**
     * @param string $nameSortie
     */
    private function filterBySearch(string $nameSortie)
    {
        if (!empty($nameSortie)) {
            foreach ($this->sortiesNonArchive as $sortie) {
                if (!str_contains(strtolower($sortie->getNom()), strtolower($nameSortie))) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }
    }

    /**
     * @param mixed $villeId
     */
    private function filterByVille(mixed $villeId)
    {
        if (!empty($villeId)) {
            foreach ($this->sortiesNonArchive as $sortie) {
                if ($villeId != $sortie->getVilleAccueil()) {
                    unset($this->sortiesNonArchive[array_search($sortie, $this->sortiesNonArchive)]);
                }
            }
        }
    }
}
