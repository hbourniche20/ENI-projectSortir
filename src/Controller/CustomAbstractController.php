<?php

    namespace App\Controller;

    use App\Entity\Sortie;
    use App\Entity\User;
    use App\Entity\Ville;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\Security\Core\Security;

    abstract class CustomAbstractController extends AbstractController implements AuthenticatedController {
        protected $session;
        protected $FORMAT_DATE = 'Y/m/d';
        protected $FORMAT_DATETIME = 'Y/m/d H:i';
        protected $FORMAT_DATETIME_WITH_SECONDE = 'Y/m/d H:i:s';
        protected $isMobile;
        protected $isTablet;

        public function __construct(RequestStack $requestStack) {
            $this->session = $requestStack->getSession();
            $this->isMobile = User::isMobileStatic();
            $this->isTablet = USer::isTablet();
        }

        // -----------------------------
        // USER
        protected function getUserBySession(): User {
            $userManager = $this->getDoctrine()->getRepository(User::class);
            $idUser = $this->session->get(Security::LAST_USERNAME);
            if (!is_null($idUser)) {
                $user = $userManager->findOneByEmail($idUser);
            } else {
                $user = $userManager->find(0);
            }
            return $user;
        }

        protected function getUserById(int $id) : ?User {
            $userManager = $this->getDoctrine()->getRepository(User::class);
            return $userManager->find($id);
        }

        protected function getUserByEmail(string $email) : ?User {
            return $this->getDoctrine()->getRepository(User::class)->findOneBy([
                'email' => $email]);
        }

        protected function isAdmin(User $user) : bool {
            foreach ($user->getRoles() as $role){
                if($role === 'ROLE_ADMIN'){
                    return true;
                }
            }
            return false;
        }
        // -----------------------------
        // SORITE
        protected function getSortieById(int $id): Sortie {
            $sortieManager = $this->getDoctrine()->getRepository(Sortie::class);
            return $sortieManager->find($id);
        }

        // -----------------------------
        // VILLE
        protected function getVilleById(int $id) : Ville{
            return $this->getDoctrine()->getRepository(Ville::class)->find($id);
        }
    }
