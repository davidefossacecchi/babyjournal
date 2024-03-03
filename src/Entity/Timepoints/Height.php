<?php

namespace App\Entity\Timepoints;

use App\Entity\Child;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class Height extends TimePoint
{
    use NumericTimepoint;

    #[ManyToOne(targetEntity: Child::class, inversedBy: 'heights')]
    private Child $child;

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): Height
    {
        $this->child = $child;
        $child->addHeight($this);
        return $this;
    }
}
