<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends CustomAbstractController
{
    #[Route('/sortie/create', name: 'creer_sortie')]
    public function create(Request $request): Response
    {
        $sortie = new Sortie($this->getUserBySession());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            return $this->validateDataAndRedirect($sortie);
        }

        return $this->render('sortie/create.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/sortie/{id}', name: 'modifier_sortie')]
    public function modifier(Request $request, int $id): Response
    {
        $sortie = $this->getSortieById($id);
        if ($sortie == null) {
            return $this->redirectToRoute('home_page');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            return $this->validateDataAndRedirect($sortie);
        }

        return $this->render('sortie/edit.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'sortieId' => $id
        ]);
    }

    #[Route('/sortie/remove/{id}', name: 'supprimer_sortie')]
    public function supprimer(Request $request, int $id): Response {
        $sortie = $this->getSortieById($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('home_page');
    }

    #[Route(path: '/sortie/show/{slug}', name: 'show_sortie', requirements: ['slug' => '\d+'])]
    public function afficher(Request $request, int $slug) : Response
    {
        $sortie = $this->getSortieById($slug);

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
            'inscrits' => $sortie->getInscrits()->getValues(),
        ]);
    }

    private function validateDataAndRedirect(Sortie $sortie) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie créée');
        return $this->redirectToRoute('home_page');
    }

}
