<?php

    namespace App\Controller\Admin;

    use App\Controller\CustomAbstractController;
    use App\Entity\User;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Serializer\Encoder\CsvEncoder;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Symfony\Component\Serializer\Serializer;

    class AddUsersController extends CustomAbstractController {
        #[Route('/admin/add/users', name: 'admin_add_users')]
        public function index(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response {
            $errors = array();
            $sucess = array();

            $usersForm = $this->createFormBuilder()
                ->add('fichier', FileType::class, [
                        'label' => 'Fichier CSV des utilisateurs :',
                        'required' => true
                    ]
                )
                ->getForm();
            $usersForm->handleRequest($request);

            if ($usersForm->isSubmitted() && $usersForm->isValid()) {
                $csv = $usersForm->get('fichier')->getData();

                $guessExtension = $csv->guessExtension();
                if ($guessExtension !== 'csv') {
                    array_push($errors, 'Votre format de fichier ".' . $guessExtension . '" ne correspond pas au format attendu qui ".csv"');
                }
                if (empty($errors)) {
                    $file = $this->getParameter('csvtmp_directory') . '/users.csv';
                    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                    //copie du csv
                    $csv->move(
                        $this->getParameter('csvtmp_directory'),
                        $file
                    );

                    $normalizers = [new ObjectNormalizer()];
                    $encoders = [
                        new CsvEncoder(),
                    ];

                    $serializer = new Serializer($normalizers, $encoders);

                    /** @var string $fileString */
                    $fileString = file_get_contents($file);

                    $data = $serializer->decode($fileString, $fileExtension);
                    array_push($sucess, 'Données bien chargées !');

                    // Création des utilisateurs en bdd
                    $usersCreated = 0;
                    $entityManager = $this->getDoctrine()->getManager();

                    foreach ($data as $row) {
                        if ($this->checkPropertyCsv('email', $row)) {
                            $email = $row['email'];
                            $user = $this->getUserByEmail($email);
                            if (!$user) {
                                // check si les properties sont bien présentes
                                if ($this->checkPropertyCsv('pseudo', $row) && $this->checkPropertyCsv('password', $row)
                                    && $this->checkPropertyCsv('prenom', $row) && $this->checkPropertyCsv('nom', $row)
                                    && $this->checkPropertyCsv('tel', $row) && $this->checkPropertyCsv('roles', $row)
                                    && $this->checkPropertyCsv('villeid', $row)
                                ) {
                                    $ville = $this->getVilleById($row['villeid']);
                                    if ($ville) {
                                        $user = new User();
                                        $user->setEmail($email)
                                            ->setVille($ville)
                                            ->setTel($row['tel'])
                                            ->setNom($row['nom'])
                                            ->setPrenom($row['prenom'])
                                            ->setPseudo($row['pseudo'])
                                            ->setRoles($this->rolesToArray($row['roles']))
                                            ->setPassword($userPasswordHasherInterface->hashPassword(
                                                $user,
                                                $row['password']
                                            ));
                                        $entityManager->persist($user);
                                        $usersCreated++;
                                        array_push($sucess, "{$user->getPseudo()}.{$user->getEmail()} est en création");
                                    } else {
                                        array_push($errors, "{$email} n'a pas été créé. Cause : ville n'existe pas");
                                    }
                                } else {
                                    array_push($errors, "{$email} n'a pas été créé. Cause : une propriété n'est pas correcte");
                                }
                            } else {
                                array_push($sucess, "{$user->getId()}.{$user->getPseudo()} existe déjà");
                            }
                        } else {
                            array_push($errors, "Utilisateur non créé. Cause : l'email est absent ou incorrect");
                        }
                    }

                    $entityManager->flush();

                    if ($usersCreated > 1) {
                        array_push($sucess, "{$usersCreated} utilisateurs ont été créé");
                    } else if ($usersCreated === 1) {
                        array_push($sucess, "1 utilisateur a été créé");
                    } else {
                        array_push($errors, "Aucun utilisateur n'a été créé");
                    }
                }
            }
            return $this->render('admin/add_users/index.html.twig', [
                'usersForm' => $usersForm->createView(),
                'errors' => $errors,
                'sucess' => $sucess,
            ]);
        }

        private function checkPropertyCsv(string $column, $row): bool {
            return array_key_exists($column, $row) && !empty($row[$column]);
        }

        private function rolesToArray(string $roles) : array {
            $string = str_replace('[', '', $roles);
            $string = str_replace(']', '', $string);
            $string = str_replace('""', '","', $string);
            $string = str_replace('"', '', $string);
            return explode(',', $string);
        }
    }
