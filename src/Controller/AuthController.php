<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
    #[Route(path: '/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signupFormAction(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        Security $security
    ): Response
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
            $security->login($user, 'form_login');
            $this->addFlash('success', 'Registrazione avvenuta con successo');
            return $this->redirectToRoute('index');
        }
        return $this->render('signup.html.twig', ['signup_form' => $form]);
    }

    #[Route(name: 'logout', path: '/logout', methods: ['GET'])]
    public function logoutAction()
    {

    }
}
