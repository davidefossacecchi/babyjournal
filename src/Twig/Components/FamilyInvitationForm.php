<?php

namespace App\Twig\Components;

use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\Family;
use App\Entity\User;
use App\Form\FamilyInvitationType;
use App\Security\Token\AuthTokenManager;
use App\Security\Voter\EntityAction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
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
        /** @var User $user */
        $user = $this->security->getUser();
        $token = $this->authTokenManager->configureForUser(FamilyInvitationToken::class, $user);
        return $this->createForm(FamilyInvitationType::class, $token);
    }

    #[LiveListener('openInvitationForm')]
    public function setFamily(#[LiveArg] Family $targetFamily): void
    {
        $this->family = $targetFamily;
        $this->dispatchBrowserEvent('modal:open', ['modalId' => 'invitation-modal']);
    }

    #[LiveAction]
    public function save(#[LiveArg] Family $family): void
    {
        $this->family = $family;
        $this->denyAccessUnlessGranted(EntityAction::VIEW->value, $this->family);
        $this->submitForm();

        $form = $this->getForm();

        if ($form->isValid()) {
            /** @var FamilyInvitationToken $token */
            $token = $form->getData();
            $this->resetForm();
            /** @var User $user */
            $user = $this->security->getUser();

            if ($user->getEmail() === $token->getEmail()) {
                $this->addFlash('invitation:error', 'Non puoi invitare te stesso');
                return;
            }

            $familyUsers = $this->family->getUsers();
            /** @var User $familyUser */
            foreach ($familyUsers as $familyUser) {
                if ($familyUser->getEmail() === $token->getEmail()) {
                    $this->addFlash('invitation:error', 'L\'utente con email "'.$familyUser->getEmail().'" è già membro della famiglia');
                    return;
                }
            }

            $invitationTokens = $this->family->getInvitations();
            /** @var FamilyInvitationToken $token */
            foreach ($invitationTokens as $invitationToken) {
                if ($invitationToken->getEmail() === $token->getEmail() && $invitationToken->isUsable()) {
                    $this->addFlash('invitation:error', 'L\'utente con email "'.$invitationToken->getEmail().'" è già stato invitato');
                    return;
                }
            }

            $token->setFamily($this->family);
            $this->authTokenManager->persist($form->getData());
            $this->addFlash('invitation:success', 'Invito inviato');
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
}
