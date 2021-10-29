<?php

    namespace App\Controller;

    use App\Entity\Image;
    use App\Form\UserFormType;
    use Symfony\Component\Form\FormError;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    class ProfilController extends CustomAbstractController {

        #[Route('/profil', name: 'profil')]
        public function index(): Response {
            return $this->profil($this->getUserBySession()->getId());
        }

        #[Route(path: '/profil/{id}', name: 'profil_id', requirements: ['id' => '\d+'])]
        public function profil(int $id): Response {
            $modifie = ($this->getUserBySession()->getId() == $id);
            $user = $this->getUserById($id);
            if (is_null($user)) {
                return $this->index();
            }
            return $this->render('profil/index.html.twig', [
                'user' => $user,
                'modifie' => $modifie
            ]);
        }

        #[Route('/profil/modif', name: 'profil_modif')]
        public function modification(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response {
            $errors = array();
            $user = $this->getUserBySession();

            $form = $this->createForm(UserFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $photo = $form->get('photo')->getData();

                // Check password
                if (!$userPasswordHasherInterface->isPasswordValid($user, $form->get('password')->getData())) {
                    array_push($errors, new FormError('Le mot de passe ne correspond pas'));
                }

                // Check forms errors
                foreach($form->getErrors(true) as $formError) {
                    if ($formError->getOrigin()->getName() != 'password') {
                        array_push($errors, $formError);
                    }
                }
                // Check photo errors
                if ($photo) {
                    // Vérification de l'extension de la photo
                    $photoExtension = $photo->guessExtension();
                    if($photoExtension !== 'jpg' && $photoExtension !== 'jpeg' && $photoExtension !== 'png' && $photoExtension !== 'gif'){
                        array_push($errors,  new FormError('Le format de l\'image est invalide. Utiliser .jpg, .jpeg, .png ou .gif'));
                    }

                    // Vérification de la taille de la photo
                    list($width, $height) = getimagesize($photo);
                    if ($width > 1920 || $height > 1080){
                        array_push($errors, new FormError('La dimension de l\'image ne doit pas être supérieur à 1920x1080'));
                    }
                }
                if (empty($errors)) {
                    $entityManager = $this->getDoctrine()->getManager();

                    // verification de si une photo existe
                    if ($photo) {
                        // on gère la photo :
                        $fichier = $user->getPseudo() . '.' . $photoExtension;
                        //copie de l'image
                        $photo->move(
                            $this->getParameter('images_directory'),
                            $fichier
                        );
                        // On stock l'image en bdd si elle n'existe pas
                        if (is_null($user->getImage())) {
                            $img = new Image();
                            $img->setName($fichier);
                            $user->setImage($img);
                        }
                        else{
                            $user->getImage()->setName($fichier);
                        }
                    }

                    $entityManager->persist($user);
                    $entityManager->flush();
                    // Attente de 1 seconde pour laisser le temps à l'img d'etre remplacer
                    sleep(1);
                    return $this->redirectToRoute('profil');
                }
                $user = $this->getUserBySession();
            }

            return $this->render('profil/profil_modif.html.twig', [
                'user' => $user,
                'userForm' => $form->createView(),
                'errors' => $errors
            ]);
        }

    }
