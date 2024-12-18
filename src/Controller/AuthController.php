<?php

namespace App\Controller;

use App\Entity\AuthToken\EmailVerificationToken;
use App\Entity\AuthToken\PasswordResetToken;
use App\Entity\User;
use App\Form\SignupForm;
use App\Repository\PasswordResetTokenRepository;
use App\Security\Token\AuthTokenManager;
use App\Serializer\AuthTokenSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

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
        MailerInterface $mailer,
        AuthTokenManager $authTokensManager
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
            $user->setEnabled(true);
            $entityManager->persist($user);
            $entityManager->flush();

            $entityManager->refresh($user);

            $token = $authTokensManager->createForUser(EmailVerificationToken::class, $user);

            $message = new TemplatedEmail();

            $message
                ->to(new Address($user->getEmail()))
                ->subject('Verifica la tua email')
                ->htmlTemplate('emails/email_verification.html.twig')
                ->context(compact('token'));

            $mailer->send($message);
            $this->addFlash('success', 'Abbiamo inviato una mail all\'indirizzo inserito. Segui il link per verificare la tua email');
            return $this->redirectToRoute('index');
        }
        return $this->render('signup.html.twig', ['signup_form' => $form]);
    }


    #[Route(name: 'logout', path: '/logout', methods: ['GET'])]
    public function logoutAction()
    {

    }

    #[Route(name: 'password_reset', path: '/password-reset', methods: ['POST', 'GET'])]
    public function passwordResetAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        AuthTokenManager $authTokensManager
    ): Response
    {

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Recupera Password'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];
            $userRepo = $entityManager->getRepository(User::class);
            $user = $userRepo->findOneBy(compact('email'));
            if (isset($user)) {
                $token = $authTokensManager->createForUser(PasswordResetToken::class, $user);

                $message = new TemplatedEmail();

                $message
                    ->to(new Address($email))
                    ->subject('Recupero password')
                    ->htmlTemplate('emails/password_reset.html.twig')
                    ->context(compact('token'));

                $mailer->send($message);
            }
            $this->addFlash('success', 'Abbiamo inviato una mail all\'indirizzo inserito');
        }

        return $this->render('password_reset.html.twig', compact('form'));
    }

    #[Route(name: 'email_verification', path: '/email-verification', methods: ['GET'])]
    public function verifyEmail(
        Request $request,
        AuthTokenSerializer $serializer,
        EntityManagerInterface $entityManager,
        AuthTokenManager $authTokensManager,
        Security $security
    ): Response
    {
        $tokenString = $request->get('t');
        try {
            $token = $serializer->deserialize($tokenString, new EmailVerificationToken());
            $token = $authTokensManager->findVerified(EmailVerificationToken::class, $token->getSelector(), $token->getPlainVerifier());
            if (empty($token)) {
                throw new \InvalidArgumentException();
            }
            $user = $token->getUser();
            $user->setVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();
            $authTokensManager->incrementUsage($token);
            $this->addFlash('success', 'Email verificata correttamente');
            $security->login($user, 'form_login');
        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', 'Link malformato o scaduto');
        }
        return $this->redirectToRoute('index');
    }

    #[Route(name: 'password_recovery', path: '/password-recovery', methods: ['GET', 'POST'])]
    public function passwordRecoveryAction(
        Request $request,
        AuthTokenSerializer $serializer,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        AuthTokenManager $authTokensManager
    )
    {
        $tokenString = $request->get('t');
        $form = null;
        try {
            $token = $serializer->deserialize($tokenString, new PasswordResetToken());

            $token = $authTokensManager->findVerified(PasswordResetToken::class, $token->getSelector(), $token->getPlainVerifier());
            if (empty($token)) {
                throw new \InvalidArgumentException();
            }

            $form = $this->createFormBuilder()
                ->add('plainPassword',
                    RepeatedType::class,
                    [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Le password devono corrispondere',
                        'required' => true,
                        'first_options' => ['label' => 'Password'],
                        'second_options' => ['label' => 'Ripeti password']
                    ])
                ->add('submit', SubmitType::class, ['label' => 'Reimposta password'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $plainPassword = $data['plainPassword'];
                $user = $token->getUser();
                $hashedPassword = $hasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();
                $authTokensManager->incrementUsage($token);
                $this->addFlash('success', 'Password aggiornata correttamente');
                return $this->redirectToRoute('app_login');
            }

        } catch (\InvalidArgumentException $e) {
            $this->addFlash('error', 'Link malformato o scaduto');
        }

        return $this->render('password_recovery.html.twig', compact('form', 'tokenString'));
    }
}
