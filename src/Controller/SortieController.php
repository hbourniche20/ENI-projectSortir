<?php

namespace App\Controller;

use App\Entity\Site;
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
        $errors = [];
        if ($sortieForm->isSubmitted()) {
            if($sortieForm->isValid()) {
                return $this->validateDataAndRedirect($sortie);
            }
            $errors = $sortieForm->getErrors(true);
        }

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'errors' => $errors,
            'sortieId' => -1,
            'villeId' => -1,
            'lieuId' => -1
        ]);
    }

    #[Route('/sortie/{id}', name: 'modifier_sortie')]
    public function edit(Request $request, int $id): Response
    {
        $sortie = $this->getSortieById($id);
        if ($sortie == null) {
            return $this->redirectToRoute('home_page');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        $errors = [];
        if ($sortieForm->isSubmitted()) {
            if ($sortieForm->isValid()) {
                return $this->validateDataAndRedirect($sortie);
            }
            $errors = $sortieForm->getErrors(true);
        }

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'errors' => $errors,
            'sortieId' => $id,
            'villeId' => $sortie->getVilleAccueil()->getId(),
            'lieuId' => $sortie->getSite()->getId()
        ]);
    }

    #[Route('/sortie/remove/{id}', name: 'supprimer_sortie')]
    public function supprimer(Sortie $sortie): Response {
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

    #[Route('/ville/{id}', name: 'select_ville')]
    public function selectVille(Request $request, int $id) {
        if ($id != null) {
            $sites = $this->getDoctrine()->getRepository(Site::class)->findByVille($id);
            $array = [];
            foreach($sites as $site) {
                array_push($array, array($site->getId(), $site->getNom()));
            }
            $response = new Response();
            $response->setContent(json_encode($array));
            return $response;

        }
        return new Response('Erreur: impossible de récuperer les données');
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

    private function validateDataAndRedirect(Sortie $sortie) : Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie créée');
        return $this->redirectToRoute('home_page');
    }

}
