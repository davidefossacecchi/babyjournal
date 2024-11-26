<?php

namespace App\Entity\AuthToken;

use App\Entity\Family;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class FamilyInvitationToken extends AuthToken implements UserInvitationTokenInterface
{
    use InvitesUsers;

    #[ORM\ManyToOne(targetEntity: Family::class, inversedBy: 'invitations')]
    private Family $family;

    public function hasReachedMaxUsages(): bool
    {
        return $this->getUsages() >= 1;
    }

    public function getTTL(): \DateInterval
    {
        return new \DateInterval('P1W');
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
