<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity]
class Child
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\LessThanOrEqual('today')]
    private \DateTimeImmutable $birthDate;

    #[ORM\ManyToOne(targetEntity: Child::class, inversedBy: 'children')]
    private Family $family;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Child
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Child
    {
        $this->name = $name;
        return $this;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): Child
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getFamily(): Family
    {
        return $this->family;
    }

    public function setFamily(Family $family): Child
    {
        $this->family = $family;
        $this->family->addChild($this);
        return $this;
    }
}
