<?php

namespace App\Controller;

use App\Entity\Family;
use App\Entity\User;
use App\Form\FamilyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FamilyController extends AbstractController
{

    #[Route(name: 'family_index', path: '/families', methods: ['GET'])]
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();
        $families = $user->getFamilies();

        return $this->render('family/index.html.twig', compact('families'));
    }

    #[Route(name: 'family_create', path: '/create-family', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(FamilyType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Family $family */
            $family = $form->getData();
            $family->addUser($user);
            $entityManager->persist($family);
            $entityManager->flush();
            $this->addFlash('success', 'Famiglia "'.$family->getName().'" aggiunta');
            return $this->redirectToRoute('family_index');
        }

        return $this->render('family/form.html.twig', compact('form'));
    }

    #[Route(name: 'family_edit', path: '/families/{id}', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'family')]
    public function edit(Request $request, Family $family, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(FamilyType::class, $family);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Family $family */
            $family = $form->getData();
            $entityManager->persist($family);
            $entityManager->flush();
            $this->addFlash('success', 'Famiglia "'.$family->getName().'" modificata');
            return $this->redirectToRoute('family_index');
        }

        return $this->render('family/form.html.twig', compact('form', 'family'));
    }

    #[Route(name: 'accept_family_invitation', path: '/family-invitation', methods: ['GET'])]
    function acceptFamilyInvitation(): Response
    {
        return new Response('ciupa');
    }
}
