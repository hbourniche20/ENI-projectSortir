<?php

namespace App\Controller\Admin;

use App\Controller\CustomAbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddUsersController extends CustomAbstractController
{
    #[Route('/admin/add/users', name: 'admin_add_users')]
    public function index(Request $request): Response {
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
            if(empty($errors)){
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
                //dump($guessExtension);
                //dd($data);
            }
        }
        return $this->render('admin/add_users/index.html.twig', [
            'usersForm' => $usersForm->createView(),
            'errors' => $errors,
            'sucess' => $sucess,
        ]);
    }
}
