<?php

namespace App\Entity;

use App\Entity\AuthToken\ChildInvitationToken;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use App\Entity\Timepoints\TimePoint;
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
    #[Assert\NotBlank]
    #[Assert\LessThanOrEqual('today')]
    private ?\DateTimeImmutable $birthDate;

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'children')]
    private Family $family;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: TimePoint::class, orphanRemoval: true)]
    private Collection $timepoints;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'representedChildren')]
    private ?User $representingUser;

    #[ORM\OneToMany(mappedBy: 'child', targetEntity: ChildInvitationToken::class)]
    private Collection $invitations;

    public function __construct()
    {
        $this->timepoints = new ArrayCollection();
        $this->invitations = new ArrayCollection();
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

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): Child
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

    public function getTimepoints(): Collection
    {
        return $this->timepoints;
    }

    public function addTimepoint(TimePoint $timePoint): Child
    {
        if (false === $this->timepoints->contains($timePoint)) {
            $this->timepoints->add($timePoint);
            $timePoint->setChild($this);
        }
        return $this;
    }

    public function removeTimepoint(TimePoint $timePoint): Child
    {
        $this->timepoints->removeElement($timePoint);
        return $this;
    }

    public function getRepresentingUser(): ?User
    {
        return $this->representingUser;
    }

    public function setRepresentingUser(?User $representingUser): Child
    {
        $this->representingUser = $representingUser;
        return $this;
    }

    public function getInvitations(): Collection
    {
        return $this->invitations;
    }

    public function addInvitation(ChildInvitationToken $invitation): Child
    {
        if (false === $this->invitations->contains($invitation)) {
            $this->invitations->add($invitation);
            $invitation->setChild($this);
        }
        return $this;
    }

    public function removeInvitation(ChildInvitationToken $invitation): Child
    {
        $this->invitations->removeElement($invitation);
        return $this;
    }

    public function hasPendingInvitations(): bool
    {
        $invitations = $this->getInvitations();
        foreach ($invitations as $invitation) {
            if ($invitation->isUsable()) {
                return true;
            }
        }
        return false;
    }
}
