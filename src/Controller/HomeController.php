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
    /**
     * *@Route("/home", name="home_page")
     */

    public function home(SortieRepository $sortieRepository, SiteRepository $siteRepository) : response
    {
        $sorties = $sortieRepository->findAll();
        $sites = $siteRepository->findAll();
        return $this->render('homepage/home.html.twig',["sorties" => $sorties,"sites"=>$sites]);

    }



}