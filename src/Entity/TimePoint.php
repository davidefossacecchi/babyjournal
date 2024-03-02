<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'type', type: Types::STRING)]
#[DiscriminatorMap(['height' => Height::class, 'weight' => Weight::class, 'body_temperature' => BodyTemperature::class])]
abstract class TimePoint
{
    use TimestampableEntity;
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::FLOAT)]
    #[Assert\GreaterThan(0)]
    private float $value;

    #[ManyToOne(targetEntity: Child::class, inversedBy: 'timepoints')]
    private Child $child;

    #[Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThanOrEqual('today')]
    private \DateTimeImmutable $date;

    #[Column(type: Types::STRING)]
    private string $notes;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): TimePoint
    {
        $this->id = $id;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): TimePoint
    {
        $this->value = $value;
        return $this;
    }

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): TimePoint
    {
        $this->child = $child;
        $child->addTimepoint($this);
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

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): TimePoint
    {
        $this->notes = $notes;
        return $this;
    }
}
