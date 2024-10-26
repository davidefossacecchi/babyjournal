<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: KernelEvents::REQUEST)]
class LoggedUserListener
{
    public function __construct(
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $routes = ['app_login', 'signup', 'password_reset', 'password_recovery'];
        $request = $event->getRequest();
        if (in_array($request->attributes->get('_route'), $routes) && $this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $url = $this->urlGenerator->generate('index');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
