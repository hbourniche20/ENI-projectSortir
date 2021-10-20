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

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'sortieId' => -1
        ]);
    }

    #[Route('/sortie/{id}', name: 'modifier_sortie')]
    public function edit(Request $request, int $id): Response
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

        return $this->render('sortie/index.html.twig', [
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

    #[Route('/ville/select', name: 'select_ville')]
    public function selectVille(Request $request) {
        $em = $this->getDoctrine()->getManager();
        echo $request;
        if($request->isXmlHttpRequest()) {
            $id = null;
            $id = $request->get('id');
            if ($id != null) {
                $ville = $em->getRepository(Ville::class)->find($id);
                $response = new Response();
                $data = json_encode($ville->getSites());
                $response->headers->set('Content-Type', 'application/json');
                $response->setContent($data);
                return $response;
            }
        }
        return new Response('Erreur');
    }

    private function validateDataAndRedirect(Sortie $sortie) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie créée');
        return $this->redirectToRoute('home_page');
    }

}
