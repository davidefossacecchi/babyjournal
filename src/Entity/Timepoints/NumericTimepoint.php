<?php

namespace App\Entity\Timepoints;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Symfony\Component\Validator\Constraints as Assert;

trait NumericTimepoint
{
    #[Column(type: Types::FLOAT)]
    #[Assert\GreaterThan(0)]
    private float $value;

    #[Column(type: Types::STRING)]
    private string $notes;

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function setNotes(string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }
}
