<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;

trait GeneratesFakeData
{
    public function getFaker(): Generator
    {
        return Factory::create('it_IT');
    }
}
