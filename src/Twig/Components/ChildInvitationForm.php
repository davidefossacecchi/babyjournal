<?php

namespace App\Twig\Components;

use App\Entity\AuthToken\ChildInvitationToken;
use App\Entity\Child;
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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ChildInvitationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Family $family;

    public ?Child $child = null;

    public function __construct(
        private readonly Security $security,
        private readonly AuthTokenManager $authTokenManager
    )
    {

    }

    private function getDataModelValue(): ?string
    {
        return 'norender|*';
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
    public function setChild(#[LiveArg] Child $child): void
    {
        $this->child = $child;
        $this->dispatchBrowserEvent('modal:open', ['modalId' => 'invitation-modal']);
    }

    #[LiveAction]
    public function save(#[LiveArg] Child $child, MailerInterface $mailer): void
    {
        $this->denyAccessUnlessGranted(EntityAction::EDIT->value, $this->family);
        $this->submitForm();

        $this->child = $child;

        $form = $this->getForm();

        if ($form->isValid()) {
            $email = $form->get('email')->getData();
            $this->resetForm();

            /** @var User $user */
            $user = $this->security->getUser();

            if ($email === $user->getEmail()) {
                $this->addFlash('invitation:error', 'Non puoi invitare te stesso');
                return;
            }

            if ($child->hasPendingInvitations()) {
                $this->addFlash('invitation:error', 'Quest* bimb* ha già un invito in sospeso');
                return;
            }

            /** @var ChildInvitationToken $token */
            $token = $this->authTokenManager->configureForUser(ChildInvitationToken::class, $user);
            $token->setChild($child);
            $token->setEmail($email);
            $this->authTokenManager->persist($token);

            $this->addFlash('invitation:success', 'Invito inviato con successo');
            $this->emit('childInvited', ['email' => $email]);

            $message = new TemplatedEmail();

            $message
                ->to($token->getEmail())
                ->subject('Invito a unirti alla famiglia "'.$this->family->getName().'"')
                ->htmlTemplate('emails/child_invitation.html.twig')
                ->context([
                    'token' => $token,
                    'family_name' => $this->family->getName(),
                    'inviter' => $user->getFirstName(),
                    'child_name' => $child->getName()
                ]);
            try {
                $mailer->send($message);
            } catch (TransportExceptionInterface|\Exception) {
                $this->addFlash('invitation:error', 'Errore nell\'invio dell\'invito, riprova');
            }
        }

    }

    #[LiveAction]
    public function close(): void
    {
        $this->dispatchBrowserEvent('modal:close');
    }
}
