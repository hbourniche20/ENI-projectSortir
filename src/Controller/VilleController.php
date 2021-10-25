<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\VilleRepository;
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
            "villes"=>$villes
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
    public function edit(Request $request, Ville $ville): Response
    {

        $name = $request->query->get('nomVille');
        $CP = $request->query->get('cpVille');
        $ville->setNom($name);
        $ville->setCodePostal($CP);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ville);
        $entityManager->flush();
        return $this->redirectToRoute('ville');
    }

    #[Route('/ville/ajouter', name: 'ajouter_ville')]
    public function new(Request $request): Response
    {
        $ville = new Ville();
        $name = $request->query->get('newNomVille');
        $CP = $request->query->get('newCpVille');
        $ville->setNom($name);
        $ville->setCodePostal($CP);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($ville);
        $entityManager->flush();

        return $this->redirectToRoute('ville');
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
}
