<?php

namespace App\Controller;

use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route(path: '', name: 'home_page')]
    public function home(SortieRepository $sortieRepository, SiteRepository $siteRepository) : response
    {
        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();
        $sortiesNonArchive = array();
        $dateNow = date("Y-m-d H:i:s");
        foreach($sorties as $sortie){
            $date = new \DateTime($sortie->getDateSortie()->format('Y-m-d H:i:s'));
            $date->add(new \DateInterval('P1M'));
            if($date > $dateNow){
                array_push($sortiesNonArchive, $sortie);
            }
        }
        return $this->render('homepage/home.html.twig',["sorties" => $sortiesNonArchive,"sites"=>$sites]);
    }




}