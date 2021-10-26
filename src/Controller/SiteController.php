<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Ville;
use App\Repository\SiteRepository;
use App\Repository\VilleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    #[Route('/site', name: 'site')]
    public function index(SiteRepository $siteRepository,VilleRepository $villeRepository): Response
    {
        $villes = $villeRepository->findAll();
        $sites = $siteRepository->findAllWithVille();;
        return $this->render('site/site.html.twig', [
            "sites"=>$sites,
            "villes"=>$villes
        ]);
    }

    #[Route('/site/remove/{id}', name: 'supprimer_site')]
    public function supprimer(Site $site): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($site);
        $entityManager->flush();
        return $this->redirectToRoute('site');
    }

    #[Route('/site/modifier/{id}', name: 'modifier_site')]
    public function edit(Request $request, Site $site,VilleRepository $villeRepository): Response
    {

        $name = $request->query->get('nomSite');
        $rue = $request->query->get('rueSite');

        $idVille = $request->query->get('ville');
        $city = $villeRepository->find($idVille);
        $site->setVille($city);
        $site->setNom($name);
        $site->setRue($rue);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($site);
        $entityManager->flush();
        return $this->redirectToRoute('site');
    }

    #[Route('/site/ajouter', name: 'ajouter_site')]
    public function new(Request $request,VilleRepository $villeRepository): Response
    {
        $site = new Site();
        $name = $request->query->get('newNomSite');
        $rue = $request->query->get('newRueSite');
        $idVille = $request->query->get('ville1');
        $city = $villeRepository->find($idVille);
        $site->setVille($city);
        $site->setNom($name);
        $site->setRue($rue);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($site);
        $entityManager->flush();

        return $this->redirectToRoute('site');
    }

    #[Route('/site/rechercher', name: 'search_site')]
    public function search(Request $request,VilleRepository $villeRepository): Response
    {
        $name = $request->query->get('site_name_search');
        $sites = $this->getDoctrine()->getRepository(Site::class)->findLikeNameSite($name);
        $villes = $villeRepository->findAll();

        return $this->render('site/site.html.twig', [
            "sites"=>$sites,
            "villes"=>$villes
        ]);
    }
}
