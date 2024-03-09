<?php

namespace App\Twig\Components;

use App\Entity\Family;
use App\Entity\Timepoints\Height;
use App\Form\TimepointType;
use App\Repository\ChildrenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class HeightTimepointForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    private ?Height $height = null;

    #[LiveProp]
    public int $familyId;

    public function __construct(private readonly ChildrenRepository $childrenRepository)
    {

    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            TimepointType::class,
            $this->height,
            [
                'data_class' => Height::class,
                'children' => $this->childrenRepository->findByFamilyId($this->familyId)
            ]
        );
    }

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        $height = $this->getForm()->getData();
        $entityManager->persist($height);
        $entityManager->flush();

        $this->addFlash('success', 'Altezza aggiunta');

        return $this->redirectToRoute('family_posts', ['id' => $this->familyId]);
    }
}
