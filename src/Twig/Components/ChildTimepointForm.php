<?php

namespace App\Twig\Components;

use App\Entity\Timepoints\BodyTemperature;
use App\Entity\Timepoints\ChildTimepoint;
use App\Entity\Timepoints\Height;
use App\Entity\Timepoints\Weight;
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
class ChildTimepointForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    private ?ChildTimepoint $timepoint = null;

    #[LiveProp]
    public int $familyId;

    #[LiveProp]
    public string $timepointType;

    public function __construct(private readonly ChildrenRepository $childrenRepository)
    {

    }

    protected function instantiateForm(): FormInterface
    {
        $dataClass = match ($this->timepointType) {
            'height' => Height::class,
            'weight' => Weight::class,
            'bodyTemperature' => BodyTemperature::class
        };
        return $this->createForm(
            TimepointType::class,
            $this->timepoint,
            [
                'data_class' => $dataClass,
                'children' => $this->childrenRepository->findByFamilyId($this->familyId)
            ]
        );
    }

    public function getTitle(): string
    {
        return match ($this->timepointType) {
            'height' => 'Aggiungi altezza',
            'weight' => 'Aggiungi peso',
            'bodyTemperature' => 'Aggiungi temperatura corporea'
        };
    }
    #[LiveAction]
    public function save(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        $timepoint = $this->getForm()->getData();
        $entityManager->persist($timepoint);
        $entityManager->flush();

        $flashMessagge = match ($this->timepointType) {
            'height' => 'Altezza aggiunta',
            'weight' => 'Peso aggiunto',
            'bodyTemperature' => 'Temperatura aggiunta'
        };

        $this->addFlash('success', $flashMessagge);

        return $this->redirectToRoute('family_posts', ['id' => $this->familyId]);
    }
}
