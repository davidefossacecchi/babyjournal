<?php

namespace App\Entity\Timepoints;

use App\Entity\Child;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Weight extends TimePoint
{
    use NumericTimepoint;

    #[ManyToOne(targetEntity: Child::class, inversedBy: 'weights')]
    private Child $child;

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): Weight
    {
        $this->child = $child;
        $child->addWeight($this);
        return $this;
    }
}
