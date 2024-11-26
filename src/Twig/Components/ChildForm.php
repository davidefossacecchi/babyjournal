<?php

namespace App\Twig\Components;

use App\Entity\Family;
use App\Security\Voter\EntityAction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Form\ChildType;
use Symfony\Component\Form\FormInterface;
use App\Entity\Child;

#[AsLiveComponent]
class ChildForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Family $family;

    public function __construct(private readonly EntityManagerInterface $em)
    {

    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ChildType::class);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->denyAccessUnlessGranted(EntityAction::EDIT->value, $this->family);
        $this->submitForm();

        $form = $this->getForm();

        if ($form->isValid()) {
            $child = $form->getData();
            $child->setFamily($this->family);
            $this->em->persist($child);
            $this->em->flush();
            $this->addFlash('child:success', 'Bimb* aggiunt* con successo');
            $this->emit('childAdded');
        }
    }

    #[LiveAction]
    public function close(): void
    {
        $this->dispatchBrowserEvent('modal:close');
    }
}
