<?php

    namespace App\Controller;

    use App\Entity\Image;
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
                $photo = $form->get('photo')->getData();
                $photoExtension = $photo->guessExtension();

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
                // Vérification de l'extension de la photo
                if($photoExtension !== 'jpg' && $photoExtension !== 'jpeg' && $photoExtension !== 'png' && $photoExtension !== 'gif'){
                    array_push($errors, 'Le format de l\'image est invalide. Utiliser .jpg, .jpeg, .png ou .gif');
                }

                // Vérification de la taille de la photo
                list($width, $height) = getimagesize($photo);
                if($width > 1920 || $height > 1080){
                    array_push($errors, 'La dimension de l\'image ne doit pas être supérieur à 1920x1080');
                }

                if (empty($errors)) {
                    $entityManager = $this->getDoctrine()->getManager();
                    // verification de si une photo existe

                    // on gère la photo :

                    $fichier = $user->getPseudo() . '.' . $photoExtension;
                    //copie de l'image
                    $photo->move(
                        $this->getParameter('images_directory'),
                        $fichier
                    );

                    // On stock l'image en bdd si elle n'existe pas
                    if(is_null($user->getImage())){
                        $img = new Image();
                        $img->setName($fichier);
                        $user->setImage($img);
                    }

                    // on hash et on upload
                    $user->setPassword(
                        $userPasswordHasherInterface->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    );
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
                'userErrorsForm' => $errors,

            ]);
        }

    }
