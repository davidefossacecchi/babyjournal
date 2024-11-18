<?php

namespace App\Twig\Components;

use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\Family;
use App\Entity\User;
use App\Security\Token\AuthTokenManager;
use App\Security\Voter\EntityAction;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FamilyInvitationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    public ?Family $family = null;
    public function __construct(
        private readonly Security $security,
        private readonly AuthTokenManager $authTokenManager
    )
    {

    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Email()
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Invita'])
            ->getForm();
    }

    #[LiveListener('openInvitationForm')]
    public function setFamily(#[LiveArg] Family $targetFamily): void
    {
        $this->family = $targetFamily;
        $this->dispatchBrowserEvent('modal:open', ['modalId' => 'invitation-modal']);
    }

    #[LiveAction]
    public function save(#[LiveArg] Family $family, MailerInterface $mailer): void
    {
        $this->family = $family;
        $this->denyAccessUnlessGranted(EntityAction::VIEW->value, $this->family);
        $this->submitForm();

        $form = $this->getForm();

        if ($form->isValid()) {
            /** @var FamilyInvitationToken $token */
            $invitedEmail = $form->getData()['email'];
            $this->resetForm();
            /** @var User $user */
            $user = $this->security->getUser();

            if ($user->getEmail() === $invitedEmail) {
                $this->addFlash('invitation:error', 'Non puoi invitare te stesso');
                return;
            }

            $familyUsers = $this->family->getUsers();
            /** @var User $familyUser */
            foreach ($familyUsers as $familyUser) {
                if ($familyUser->getEmail() === $invitedEmail) {
                    $this->addFlash('invitation:error', 'L\'utente con email "'.$familyUser->getEmail().'" è già membro della famiglia');
                    return;
                }
            }

            $invitationTokens = $this->family->getInvitations();
            /** @var FamilyInvitationToken $token */
            foreach ($invitationTokens as $invitationToken) {
                if ($invitationToken->getEmail() === $invitedEmail && $invitationToken->isUsable()) {
                    $this->addFlash('invitation:error', 'L\'utente con email "'.$invitationToken->getEmail().'" è già stato invitato');
                    return;
                }
            }
            $token = $this->initToken();
            $token->setEmail($invitedEmail);
            $token->setFamily($this->family);
            $this->authTokenManager->persist($token);
            $this->addFlash('invitation:success', 'Invito inviato');
            $message = new TemplatedEmail();

            $message
                ->to($token->getEmail())
                ->subject('Invito a unirti alla famiglia "'.$this->getFamilyName().'"')
                ->htmlTemplate('emails/family_invitation.html.twig')
                ->context([
                    'token' => $token,
                    'family_name' => $this->family->getName(),
                    'inviter' => $user->getFirstName()
                ]);
            $mailer->send($message);
        }
    }

    #[LiveAction]
    public function close(): void
    {
        $this->dispatchBrowserEvent('modal:close');
    }

    public function getFamilyName(): ?string
    {
        return $this->family?->getName();
    }

    private function initToken(): FamilyInvitationToken
    {
        /** @var User $user */
        $user = $this->security->getUser();
        /** @var FamilyInvitationToken $token */
        $token = $this->authTokenManager->configureForUser(FamilyInvitationToken::class, $user);
        return $token;
    }
}
