<?php

namespace App\Entity\Timepoints;

use App\Entity\Child;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class BodyTemperature extends TimePoint
{
    use NumericTimepoint;
    #[ManyToOne(targetEntity: Child::class, inversedBy: 'bodyTemperatures')]
    private Child $child;

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): BodyTemperature
    {
        $this->child = $child;
        $child->addBodyTemperature($this);
        return $this;
    }
}
