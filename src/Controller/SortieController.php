<?php

namespace App\Controller;

use App\Entity\Site;
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
            'sortieId' => -1,
            'villeId' => -1,
            'lieuId' => -1
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
            'sortieId' => $id,
            'villeId' => $sortie->getVilleAccueil()->getId(),
            'lieuId' => $sortie->getSite()->getId()
        ]);
    }

    #[Route('/sortie/remove/{id}', name: 'supprimer_sortie')]
    public function supprimer(Request $request, Sortie $sortie): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sortie);
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

    private function validateDataAndRedirect(Sortie $sortie) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie créée');
        return $this->redirectToRoute('home_page');
    }

}
