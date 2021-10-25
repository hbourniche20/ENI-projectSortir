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
        #[Route('/sortie/create', name: 'creer_sortie')]
        public function create(Request $request): Response {
            $sortie = new Sortie($this->getUserBySession());
            $sortieForm = $this->createForm(SortieType::class, $sortie);
            $sortieForm->handleRequest($request);

            if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
                $sortie->setPubliee($request->get('button') === 'publier');
                $sortie->setMotifAnnulation(null);
                return $this->validateDataAndRedirect($sortie);
            }

            $sortie->setVilleAccueil(new Ville());
            $sortie->setSite(new Site());

            return $this->render('sortie/index.html.twig', [
                'controller_name' => 'SortieController',
                'sortieForm' => $sortieForm->createView(),
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

            return $this->render('sortie/index.html.twig', [
                'controller_name' => 'SortieController',
                'sortieForm' => $sortieForm->createView(),
                'sortie' => $sortie,
                'villeId' => $sortie->getVilleAccueil()->getId(),
                'lieuId' => $sortie->getSite()->getId()
            ]);
        }

        #[Route(path: '/sortie/publiee/{slug}', name: 'publiee_sortie')]
        public function publiee(int $slug): Response {
            $sortie = $this->getSortieById($slug);
            $sortie->setPubliee(true);
            return $this->persistAndRedirect($sortie, 'home_page', null);
        }

        #[Route(path: '/sortie/annuler/{slug}', name: 'annuler_sortie', requirements: ['slug' => '\d+'])]
        public function anuller(Request $request, int $slug): Response {
            $sortie = $this->getSortieById($slug);
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

        #[Route('/sortie/remove/{slug}', name: 'supprimer_sortie')]
        public function supprimer(int $slug): Response {
            $sortie = $this->getSortieById($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('home_page');
        }

        #[Route('/sortie/inscrire/{id}', name: 'inscription')]
        public function inscrire(Request $request, int $id): Response {
            $user = $this->getUserBySession();
            $sortieInscrit = $this->getSortieById($id);
            $entityManager = $this->getDoctrine()->getManager();
            $sortieInscrit->addInscrit($user);
            $entityManager->persist($sortieInscrit);
            $entityManager->flush();
            return $this->redirectToRoute('home_page');
        }

        #[Route('/sortie/desister/{id}', name: 'desinscription')]
        public function desister(Request $request, int $id): Response {
            $user = $this->getUserBySession();
            $sortieInscrits = $this->getSortieById($id);
            $entityManager = $this->getDoctrine()->getManager();
            $sortieInscrits->removeInscrit($user);
            $entityManager->persist($sortieInscrits);
            $entityManager->flush();
            return $this->redirectToRoute('home_page');
        }

        #[Route('/ville/{id}', name: 'select_ville')]
        public function selectVille(Request $request, int $id) {
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

        #[Route(path: '/sortie/show/{slug}', name: 'show_sortie', requirements: ['slug' => '\d+'])]
        public function afficher(Request $request, int $slug): Response {
            $sortie = $this->getSortieById($slug);

            $idUserQuiCreeLaSortie = $sortie->getOrganisateur()->getId();
            $idUserConnecte = $this->getUserBySession()->getId();
            $inscritALaSortie = false;

            foreach ($sortie->getInscrits()->getValues() as $inscrit) {
                dump($inscrit->getId());
                dump($idUserConnecte === $idUserQuiCreeLaSortie);
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
            ]);
        }

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
