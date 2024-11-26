<?php

namespace App\Twig\Components;

use App\Entity\Child;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ChildCard
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Child $child;


    #[LiveAction]
    public function openInvitationForm(): void
    {
        $this->emit('openInvitationForm', ['child' => $this->child->getId()]);
    }
}
