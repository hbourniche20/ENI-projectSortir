<?php

    namespace App\Controller;

    use App\Entity\User;
    use App\Form\RegistrationFormType;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class RegistrationController extends AbstractController {
        #[Route('/register', name: 'user_register')]
        public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);
            $errors = [];
            if ($form->isSubmitted() && $form->isValid()) {
                $this->persistData($userPasswordHasherInterface, $user, $form);
                return $this->redirectToRoute('app_login');
            }
            $errors = $form->getErrors(true);

            return $this->render('registration/register.html.twig', [
                'registerForm' => $form->createView(),
                'errors' => $errors
            ]);
        }

        private function persistData(UserPasswordHasherInterface $userPasswordHasherInterface, $user, $form) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(["ROLE_USER"]);
            $user->setDesactiver(false);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
    }
