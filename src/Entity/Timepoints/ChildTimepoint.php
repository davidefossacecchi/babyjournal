<?php

namespace App\Entity\Timepoints;

use App\Entity\Child;

abstract class ChildTimepoint extends TimePoint
{
    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): ChildTimepoint
    {
        $this->child = $child;
        $child->addTimepoint($this);
        return $this;
    }
}
