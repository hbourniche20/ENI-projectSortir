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
    #[Route(path: '', name: 'home_page')]
    public function home(SortieRepository $sortieRepository, VilleRepository $villeRepository, Request $request): response
    {

        $sorties = $sortieRepository->findAll();
        $villes = $villeRepository->findAll();
        $sortiesNonArchive = array();
        $dateNow = date("Y-m-d H:i:s");
        foreach ($sorties as $sortie) {
            $date = new \DateTime($sortie->getDateSortie()->format('Y-m-d H:i:s'));
            $date->add(new \DateInterval('P1M'));
            if ($date > $dateNow) {
                array_push($sortiesNonArchive, $sortie);
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

            // Affiche que les sorties dont la ville est le site choisi
            if (!empty($villeId)) {
                foreach ($sortiesNonArchive as $sortie) {
                    if ($villeId != $sortie->getVilleAccueil()) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            }

            //Affiche que les sorties avec la chaine de caractère comprise dans le nom de la sortie
            if (!empty($nameSortie)) {
                foreach ($sortiesNonArchive as $sortie) {
                    if (strpos(strtolower($sortie->getNom()), strtolower($nameSortie)) === false) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            }

            //Affiche seulement les sorties comprises entre les deux dates
            if (!empty($dateDebut) && empty($dateFin)) {

                foreach ($sortiesNonArchive as $sortie) {
                    if (date_format($dateDebut,'Y/m/d') >= date_format($sortie->getDateSortie(), 'Y/m/d')) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            } else if (empty($dateDebut) && !empty($dateFin)) {
                foreach ($sortiesNonArchive as $sortie) {
                    if (date_format($dateFin, 'Y/m/d') <= date_format($sortie->getDateSortie(), 'Y/m/d')) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            } else if (!empty($dateDebut) && !empty($dateFin)) {
                foreach ($sortiesNonArchive as $sortie) {
                    if (date_format($dateDebut,'Y/m/d') >= date_format($sortie->getDateSortie(), 'Y/m/d') || date_format($dateFin,'Y/m/d') <= date_format($sortie->getDateSortie(), 'Y/m/d')) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            }

            $userSession = $this->getUserBySession();

            // Affiche que les sorties dont l'user est l'organisateur
            if (!empty($organisateur) && $organisateur) {
                foreach ($sortiesNonArchive as $sortie) {
                    if ($sortie->getOrganisateur() != $userSession) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            }
            // affichée que les sorties inscrit et/ou non inscrit
            if ($inscrit) {
                $estInscrit = false;
                foreach ($sortiesNonArchive as $sortie) {
                    foreach ($sortie->getInscrits() as $ins) {
                        if ($ins == $userSession) {
                            $estInscrit = true;
                            break;
                        }
                    }
                    if (!$estInscrit) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                    $estInscrit = false;
                }
            }

            if ($nonInscrit) {
                foreach ($sortiesNonArchive as $sortie) {
                    foreach ($sortie->getInscrits() as $ins) {
                        if ($ins == $userSession) {
                            unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                            break;
                        }
                    }
                }
            }
            // affichée que les sorties passées
            if ($sortiePassees) {
                $dateActuelle = date('Y/m/d H:i');
                foreach ($sortiesNonArchive as $sortie) {
                    if ($sortie->getDateSortie() < $dateActuelle) {
                        unset($sortiesNonArchive[array_search($sortie, $sortiesNonArchive)]);
                    }
                }
            }
        }
        return $this->render('homepage/home.html.twig', ["sorties" => $sortiesNonArchive, "sites" => $villes, "searchForm" => $searchForm->createView()]);
    }


}