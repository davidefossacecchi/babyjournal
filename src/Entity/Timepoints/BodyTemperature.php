<?php

namespace App\Entity\Timepoints;

use Doctrine\ORM\Mapping\Entity;

#[Entity]
class BodyTemperature extends ChildTimepoint
{
    use NumericTimepoint;

}
