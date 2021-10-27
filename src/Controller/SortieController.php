<?php

    namespace App\Controller;

    use App\Entity\Site;
    use App\Entity\Sortie;
    use App\Entity\Ville;
    use App\Form\SortieType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

class SortieController extends CustomAbstractController {


    // *****************************************************************************************************************
    // ROUTES AVEC REDIRECTIONS
    // *****************************************************************************************************************
    #[Route('/sortie/create', name: 'creer_sortie')]
    public function create(Request $request): Response
    {
        $sortie = new Sortie($this->getUserBySession());
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        $errors = [];
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setPubliee($request->get('button') === 'publier');
            $sortie->setMotifAnnulation(null);
            return $this->validateDataAndRedirect($sortie);
        }
        $errors = $sortieForm->getErrors(true);
        $sortie->setVilleAccueil(new Ville());
        $sortie->setSite(new Site());

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'errors' => $errors,
            'sortie' => $sortie,
        ]);
    }

    #[Route('/sortie/{id}', name: 'modifier_sortie')]
    public function edit(Request $request, int $id): Response {
        $sortie = $this->getSortieById($id);
        if ($sortie == null) {
            return $this->redirectToRoute('home_page');
        }
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            return $this->validateDataAndRedirect($sortie);
        }
        $errors = $sortieForm->getErrors(true);
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sortieForm' => $sortieForm->createView(),
            'errors' => $errors,
            'sortie' => $sortie,
            'villeId' => $sortie->getVilleAccueil()->getId(),
            'lieuId' => $sortie->getSite()->getId()
        ]);
    }

        #[Route(path: '/sortie/publiee/{id}', name: 'publiee_sortie')]
        public function publiee(Sortie $sortie): Response {
            $sortie->setPubliee(true);
            return $this->persistAndRedirect($sortie, 'home_page', null);
        }

        #[Route(path: '/sortie/annuler/{id}', name: 'annuler_sortie', requirements: ['id' => '\d+'])]
        public function annuler(Request $request, Sortie $sortie): Response {
            $error = '';
            $form = $this->createFormBuilder()
                ->add('motif', TextareaType::class, ['label' => 'Motif :'])
                ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $motif = trim(strip_tags($form->get('motif')->getData()));
                if (strlen($motif) > 0) {
                    $sortie->setMotifAnnulation($motif);
                    return $this->persistAndRedirect($sortie, 'home_page', null);
                }
                $error = 'Le motif de l\'annulation ne doit pas être vide';
            }
            return $this->render('sortie/cancel.html.twig', [
                'sortie' => $sortie,
                'annuleForm' => $form->createView(),
                'error' => $error,
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
        public function inscrire(int $id): Response {
            $user = $this->getUserBySession();
            $sortieInscrit = $this->getSortieById($id);
            $entityManager = $this->getDoctrine()->getManager();
            $sortieInscrit->addInscrit($user);
            $entityManager->persist($sortieInscrit);
            $entityManager->flush();
            return $this->redirectToRoute('home_page');
        }

        #[Route('/sortie/desister/{id}', name: 'desinscription')]
        public function desister(Sortie $sortie): Response {
            $user = $this->getUserBySession();
            $entityManager = $this->getDoctrine()->getManager();
            $sortie->removeInscrit($user);
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('home_page');
        }

        #[Route(path: '/sortie/show/{id}', name: 'show_sortie', requirements: ['id' => '\d+'])]
        public function afficher(Sortie $sortie): Response {
            $user = $this->getUserBySession();
            $idUserQuiCreeLaSortie = $sortie->getOrganisateur()->getId();
            $idUserConnecte = $user->getId();
            $inscritALaSortie = false;

            foreach ($sortie->getInscrits()->getValues() as $inscrit) {
                if ($inscrit->getId() === $idUserConnecte) {
                    $inscritALaSortie = true;
                    break;
                }
            }
            return $this->render('sortie/show.html.twig', [
                'sortie' => $sortie,
                'inscrits' => $sortie->getInscrits()->getValues(),
                'inscritALaSortie' => $inscritALaSortie,
                'idUserConnecte' => $idUserConnecte,
                'isAdmin' => $this->isAdmin($user),
            ]);
        }

        // *****************************************************************************************************************
        // ROUTES AVEC JSON
        // *****************************************************************************************************************

        #[Route('/select/ville/{id}', name: 'select_ville')] // Attention ici, cette route est écrite en dur dans le JS
        public function selectVille(int $id) {
            if ($id != null) {
                $sites = $this->getDoctrine()->getRepository(Site::class)->findByVille($id);
                $array = [];
                foreach ($sites as $site) {
                    array_push($array, array($site->getId(), $site->getNom()));
                }
                $response = new Response();
                $response->setContent(json_encode($array));
                return $response;
            }
            return new Response('Erreur: impossible de récuperer les données');
        }

        #[Route('/select/sortie/{id}', name: 'search_sortie')] // Attention ici, cette route est écrite en dur dans le JS
        public function infos(Sortie $sortie): Response
        {
            if ($sortie != null) {
                $array = [$sortie->getVilleAccueil()->getId(), $sortie->getSite()->getId()];

                $response = new Response();
                $response->setContent(json_encode($array));
                return $response;
            }
            return new Response('Erreur: impossible de récuperer les données');
        }
        // *****************************************************************************************************************
        // PRIVATE FUNCTIONS
        // *****************************************************************************************************************

        private function validateDataAndRedirect(Sortie $sortie): Response {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'Sortie créée');
            return $this->redirectToRoute('home_page');
        }

        private function persistAndRedirect(Sortie $sortie, $route, ?array $parametre): Response {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            if (!empty($parametre)) {
                return $this->redirectToRoute($route, $parametre);
            }
            return $this->redirectToRoute($route);
        }

    }
