<?php

    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

    class SecurityController extends AbstractController {

        #[Route(path: '/login', name: 'app_login')]
        public function login(AuthenticationUtils $authenticationUtils): Response {
             if ($this->getUser()) {
               return $this->redirectToRoute('home_page');
            }
            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();
            // last username entered by the user
            $lastUsername = (array_key_exists('seSouvenirDeMoi', $_COOKIE))? $_COOKIE['seSouvenirDeMoi'] : '';

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
                'check' => ($lastUsername != ''),
            ]);
        }

        #[Route(path: '/logout', name: 'app_logout')]
        public function logout(): void {
            throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        }

        #[Route(path: '/desactivate', name: 'desactivate')]
        public function desactivate() : Response {
            return $this->render('security/desactivate.html.twig');
        }
    }
