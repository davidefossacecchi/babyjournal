<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login', methods: ['GET', 'POST'])]
    public function loginAction(): Response
    {
        return $this->redirect($this->generateUrl('signup'));
    }
    #[Route(path: '/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signupFormAction(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(SignupForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $plainPassword = $user->getPlainPassword();
            $user->eraseCredentials();
            $user->setPassword($hasher->hashPassword($user, $plainPassword));
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Registrazione avvenuta con successo');
        }
        return $this->render('signup.html.twig', ['signup_form' => $form]);
    }
}
