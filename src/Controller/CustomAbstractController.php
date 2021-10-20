<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

abstract class CustomAbstractController extends AbstractController
{
    protected $session;

    public function __construct(RequestStack $requestStack) {
        $this->session = $requestStack->getSession();
    }

    protected function getUserBySession(): User {
        $userManager = $this->getDoctrine()->getRepository(User::class);
        $idUser = $this->session->get(Security::LAST_USERNAME);
        if (!is_null($idUser)) {
            $user = $userManager->findOneByEmail($idUser);
        } else {
            $user = $userManager->find(0);
        }
        return $user;
    }
}
