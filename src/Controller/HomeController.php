<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(name: 'index', path: '/', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $families = $user->getFamilies();

        if (count($families) === 1) {
            return $this->redirectToRoute('family_posts', ['id' => $families->first()->getId()]);
        }
        return $this->redirectToRoute('family_index');
    }
}
