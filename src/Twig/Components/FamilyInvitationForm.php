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
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FamilyInvitationForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
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

    #[LiveAction]
    public function save(FormInterface $form): void
    {
        $this->denyAccessUnlessGranted(EntityAction::VIEW->value, $this->family);
        $this->submitForm();

        $form = $this->getForm();

        if ($form->isValid()) {
            $this->authTokenManager->persist($form->getData());
            $this->addFlash('invitation:success', 'Invito inviato');
        }
    }

    #[LiveAction]
    public function close(): void
    {
        $this->dispatchBrowserEvent('modal:close');
    }
}
