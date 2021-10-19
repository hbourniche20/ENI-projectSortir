<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(RequestStack $requestStack): Response
    {
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $session = $requestStack->getSession();

        $idUser = $session->get('id');

        if(!is_null($idUser)){
            $user = $userManager->find($idUser);
        } else {
            $user = $userManager->find(0);
        }

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'test',
            'user' => $user,
        ]);
    }
}
