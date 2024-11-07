<?php

namespace App\Tests\Application;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

trait FixesLiveComponentsSession
{
    protected ContainerInterface $container;
    public function setUpComponentsSession(): void
    {
        $session = new Session(new MockFileSessionStorage());
        $request = new Request();
        $request->setSession($session);
        $stack = $this->container->get(RequestStack::class);
        $stack->push($request);
    }
}
