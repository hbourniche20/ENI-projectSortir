<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\SortieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateSortieController extends AbstractController
{
    #[Route('/create/sortie', name: 'create_sortie')]
    public function create(Request $request): Response
    {
        $villeRepo = $this->getDoctrine()->getRepository(Ville::class);

        // TODO GET USER VILLE
        $sortie = new Sortie($villeRepo->findAll()[0]);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('home_page');
        }

        return $this->render('create_sortie/index.html.twig', [
            'controller_name' => 'CreateSortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }
}
