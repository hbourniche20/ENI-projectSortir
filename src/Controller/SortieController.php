<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
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
    public function afficher(Request $request, int $id): Response
    {
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
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
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sortie);
        $entityManager->flush();
        return $this->redirectToRoute('home_page');
    }

    #[Route('/sortie/inscrire/{id}', name: 'inscription')]
    public function inscrire(Request $request, int $id): Response {
        $user = $this->getUserBySession();
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortieInscrit = $sortieRepo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $sortieInscrit ->addInscrit($user);
        $entityManager->persist($sortieInscrit);
        $entityManager->flush();
        return $this->redirectToRoute('home_page');
    }

    #[Route('/sortie/desister/{id}', name: 'desinscription')]
    public function desister(Request $request, int $id): Response {
        $user = $this->getUserBySession();
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortieInscrits = $sortieRepo->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $sortieInscrits ->removeInscrit($user);
        $entityManager->persist($sortieInscrits);
        $entityManager->flush();
        return $this->redirectToRoute('home_page');
    }

    private function validateDataAndRedirect(Sortie $sortie) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie créée');
        return $this->redirectToRoute('home_page');
    }

}
