<?php

namespace App\Controller;

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

    public function home(SortieRepository $sortieRepository) : response
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('homepage/home.html.twig',["sorties" => $sorties]);

    }




}