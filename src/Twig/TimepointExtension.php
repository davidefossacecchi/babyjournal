<?php

namespace App\Twig;

use App\Entity\Timepoints\Birthday;
use App\Entity\Timepoints\BodyTemperature;
use App\Entity\Timepoints\Height;
use App\Entity\Timepoints\Weight;
use App\Entity\Timepoints\TimePoint;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class TimepointExtension extends AbstractExtension
{

    public function getTests(): array
    {
        return [
            new TwigTest('bodyTemperature', fn (TimePoint $t) => $t instanceof BodyTemperature),
            new TwigTest('height', fn (TimePoint $t) => $t instanceof Height),
            new TwigTest('weight', fn (TimePoint $t) => $t instanceof Weight),
            new TwigTest('birthday', fn (TimePoint $t) => $t instanceof Birthday),
        ];
    }

}
