<?php

namespace App\Entity;

use App\Entity\Timepoints\BodyTemperature;
use App\Entity\Timepoints\Height;
use App\Entity\Timepoints\TimePoint;
use App\Entity\Timepoints\Weight;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
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

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'children')]
    private Family $family;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: BodyTemperature::class, orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $bodyTemperatures;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: Height::class, orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $heights;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: Weight::class, orphanRemoval: true)]
    #[ORM\OrderBy(['date' => 'ASC'])]
    private Collection $weights;

    public function __construct()
    {
        $this->bodyTemperatures = new ArrayCollection();
        $this->heights = new ArrayCollection();
        $this->weights = new ArrayCollection();
    }
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

    public function getBodyTemperatures(): Collection
    {
        return $this->bodyTemperatures;
    }

    public function getHeights(): Collection
    {
        return $this->heights;
    }

    public function getWeights(): Collection
    {
        return $this->weights;
    }

    public function addBodyTemperature(BodyTemperature $timePoint): Child
    {
        if (false === $this->bodyTemperatures->contains($timePoint)) {
            $this->bodyTemperatures->add($timePoint);
            $timePoint->setChild($this);
        }
        return $this;
    }

    public function removeBodyTemperature(BodyTemperature $timePoint): Child
    {
        $this->bodyTemperatures->removeElement($timePoint);
        return $this;
    }

    public function addHeight(Height $timePoint): Child
    {
        if (false === $this->heights->contains($timePoint)) {
            $this->heights->add($timePoint);
            $timePoint->setChild($this);
        }
        return $this;
    }

    public function removeHeight(Height $timePoint): Child
    {
        $this->heights->removeElement($timePoint);
        return $this;
    }

    public function addWeight(Weight $timePoint): Child
    {
        if (false === $this->weights->contains($timePoint)) {
            $this->weights->add($timePoint);
            $timePoint->setChild($this);
        }
        return $this;
    }

    public function removeWeight(Weight $timePoint): Child
    {
        $this->weights->removeElement($timePoint);
        return $this;
    }
}
