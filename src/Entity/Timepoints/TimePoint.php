<?php

namespace App\Entity\Timepoints;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[DiscriminatorMap([
    'height' => Height::class,
    'weight' => Weight::class,
    'body_temperature' => BodyTemperature::class,
    'post' => Post::class
])]
abstract class TimePoint
{
    use TimestampableEntity;
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\LessThanOrEqual('today')]
    private \DateTimeImmutable $date;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TimePoint
    {
        $this->id = $id;
        return $this;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): TimePoint
    {
        $this->date = $date;
        return $this;
    }
}
