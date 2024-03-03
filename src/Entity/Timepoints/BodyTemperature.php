<?php

namespace App\Entity\Timepoints;

use App\Entity\Child;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
class BodyTemperature extends ChildTimepoint
{
    use NumericTimepoint;

}
