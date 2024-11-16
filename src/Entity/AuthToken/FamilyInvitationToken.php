<?php

namespace App\Entity\AuthToken;

use App\Entity\Family;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class FamilyInvitationToken extends AuthToken
{
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    #[ORM\ManyToOne(targetEntity: Family::class)]
    private Family $family;

    public function hasReachedMaxUsages(): bool
    {
        return $this->getUsages() >= 1;
    }

    public function getTTL(): \DateInterval
    {
        return new \DateInterval('P1W');
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): FamilyInvitationToken
    {
        $this->email = $email;
        return $this;
    }

    public function getFamily(): Family
    {
        return $this->family;
    }

    public function setFamily(Family $family): FamilyInvitationToken
    {
        $this->family = $family;
        return $this;
    }
}
