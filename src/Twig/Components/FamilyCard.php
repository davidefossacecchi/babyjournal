<?php

namespace App\Twig\Components;

use App\Entity\Family;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FamilyCard
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public Family $family;

    #[LiveAction]
    public function openInvitationForm(): void
    {
        $this->emit('openInvitationForm', ['targetFamily' => $this->family->getId()]);
    }
}
