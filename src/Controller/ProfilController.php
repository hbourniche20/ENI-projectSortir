<?php

    namespace App\Controller;

    use App\Form\UserFormType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class ProfilController extends CustomAbstractController {

        #[Route('/profil', name: 'profil')]
        public function index(): Response {
            $user = $this->getUserBySession();
            return $this->render('profil/index.html.twig', [
                'user' => $user,
            ]);
        }

        #[Route('/profil/modif', name: 'profil_modif')]
        public function modification(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response {
            $errors = array();
            $user = $this->getUserBySession();

            $form = $this->createForm(UserFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $email = trim(strip_tags($form->get('email')->getData()));
                $confirmation = trim(strip_tags($form->get('confirmation')->getData()));
                $password = trim(strip_tags($form->get('password')->getData()));

                //vérifie les données
                // email
                if (!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $email)) {
                    array_push($errors, 'L\'email doit correspondre au format \'example@email.com\'.');
                }
                // password == confirmation
                if ($password !== $confirmation) {
                    array_push($errors, 'Le mot de passe doit correspondre a la confirmation.');
                }
                // password + de 6 caractere
                if (strlen($password) < 6) {
                    array_push($errors, 'Le mot de passe doit faire plus de 6 caractères.');
                }

                if (empty($errors)) {

                    // on hash et on upload
                    $user->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                    return $this->redirectToRoute('profil');
                }
                $user = $this->getUserBySession();
            }

            return $this->render('profil/profil_modif.html.twig', [
                'user' => $user,
                'userForm' => $form->createView(),
                'userErrorsForm' => $errors,

            ]);
        }

    }
