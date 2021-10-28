<?php

    namespace App\EventSubscriber;

    use App\Controller\AuthenticatedController;
    use App\Entity\User;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpKernel\Event\ControllerEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    use Symfony\Component\Security\Core\Security;

    class ConnectionSubscriber extends AbstractController implements EventSubscriberInterface {

        private UrlGeneratorInterface $urlGenerator;

        private $session;

        public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator) {
            $this->session = $requestStack->getSession();
            $this->urlGenerator = $urlGenerator;
        }

        public function onKernelController(ControllerEvent $event)
        {
            $controller = $event->getController();

            // when a controller class defines multiple action methods, the controller
            // is returned as [$controllerInstance, 'methodName']
            if (is_array($controller)) {
                $controller = $controller[0];
            }

            if ($controller instanceof AuthenticatedController) {
                $idUser = $this->session->get(Security::LAST_USERNAME);
                if (!is_null($idUser)) {
                    $userManager = $this->getDoctrine()->getRepository(User::class);
                    $user = $userManager->findOneByEmail($idUser);
                    if($user->getDesactiver()){
                        $event->setController(function() {
                            return new RedirectResponse($this->urlGenerator->generate('desactivate'));
                        });
                    }
                }
            }
        }

        public static function getSubscribedEvents() {
            return [
                KernelEvents::CONTROLLER => 'onKernelController',
            ];
        }
    }