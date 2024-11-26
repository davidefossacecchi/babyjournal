<?php

namespace App\Twig\Components;

use App\Entity\Family;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class ChildList
{
    use DefaultActionTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Family $family;

    #[LiveListener('childAdded')]
    #[LiveListener('childrenInvited')]
    public function onChildrenStatusChange(): void
    {

    }

    #[LiveAction]
    public function openAddForm(): void
    {
        $this->dispatchBrowserEvent('modal:open', ['modalId' => 'add-modal']);
    }
}
