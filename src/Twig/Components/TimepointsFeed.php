<?php

namespace App\Twig\Components;

use App\Entity\Family;
use App\Pagination\Pagination;
use App\Repository\TimepointsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class TimepointsFeed
{
    use DefaultActionTrait;

    #[LiveProp]
    public int $page = 1;

    #[LiveProp]
    public ?Family $family = null;

    public Pagination $timepoints;



    public function __construct(
        private readonly TimepointsRepository $timepointsRepository
    )
    {
        $this->timepoints = new Pagination([], 0, 0);
    }

    public function mount(Family $family, int $page): void
    {
        $this->family = $family;
        $this->page = $page;
        $this->loadTimepoints();
    }

    #[LiveListener('timepointAdded')]
    public function loadTimepoints(): void
    {
        $this->timepoints = $this->timepointsRepository->getAllByFamily($this->family, $this->page);
    }
}
