<?php

namespace App\Tests\Type;

use App\Entity\User;
use App\Form\TimepointType;
use Symfony\Component\Form\Test\TypeTestCase;

class TimepointTypeTest extends TypeTestCase
{
    /**
     * Tests that the TimepointType is restricted to Timepoints entities.
     * @return void
     */
    public function testRestrictedTimepointType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create(TimepointType::class, null, ['data_class' => User::class]);
    }
}
