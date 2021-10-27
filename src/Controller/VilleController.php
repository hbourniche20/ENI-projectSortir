<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'ville')]
    public function index(VilleRepository $villeRepository): Response
    {

        $villes = $villeRepository->findAll();
        return $this->render('ville/ville.html.twig', [
            "villes"=>$villes,
            "errors"=>array()
        ]);
    }

    #[Route('/ville/remove/{id}', name: 'supprimer_ville')]
    public function supprimer(Ville $ville): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->redirectToRoute('ville');
    }

    #[Route('/ville/modifier/{id}', name: 'modifier_ville')]
    public function edit(Request $request, Ville $ville,VilleRepository $villeRepository): Response
    {

        $name = $request->query->get('nomVille');
        $CP = $request->query->get('cpVille');

        return $this->checkAndPersistDataVille($name, $CP, $ville, $villeRepository);

    }

    #[Route('/ville/ajouter', name: 'ajouter_ville')]
    public function new(Request $request,VilleRepository $villeRepository): Response
    {
        $ville = new Ville();
        $name = $request->query->get('newNomVille');
        $CP = $request->query->get('newCpVille');

        return $this->checkAndPersistDataVille($name, $CP, $ville, $villeRepository);
    }

    #[Route('/ville/rechercher', name: 'search_ville')]
    public function search(Request $request): Response
    {
        $name = $request->query->get('ville_name_search');
        $villes = $this->getDoctrine()->getRepository(Ville::class)->findLikeName($name);

        return $this->render('ville/ville.html.twig', [
            "villes"=>$villes
        ]);
    }

    private function checkAndPersistDataVille(String $name, String $CP, Ville $ville, VilleRepository $villeRepository){
        $villes = $villeRepository->findAll();
        $errors = array();

        if($name ==''){
            array_push($errors, new FormError('Le nom de la ville n\'a pas été renseigné'));
        }

        if( strlen($CP) != 5 or !ctype_digit($CP)  ){
            array_push($errors, new FormError('Le code postal doit contenir 5 chiffres'));
        }

        if (empty($errors)) {
            $ville->setNom($name);
            $ville->setCodePostal($CP);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('ville');
        }
        return $this->render('ville/ville.html.twig', [
            'errors' => $errors,
            "villes"=>$villes
        ]);
    }
}
