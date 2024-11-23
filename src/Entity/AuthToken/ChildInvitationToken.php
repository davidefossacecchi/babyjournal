<?php

namespace App\Entity\AuthToken;

use App\Entity\Child;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ChildInvitationToken extends AuthToken implements UserInvitationTokenInterface
{
    use InvitesUsers;


    #[ORM\ManyToOne(targetEntity: Child::class, inversedBy: 'invitations')]
    private Child $child;

    public function hasReachedMaxUsages(): bool
    {
        return $this->getUsages() >= 1;
    }

    public function getTTL(): \DateInterval
    {
        return new \DateInterval('P1W');
    }

    public function getChild(): Child
    {
        return $this->child;
    }

    public function setChild(Child $child): ChildInvitationToken
    {
        $this->child = $child;
        return $this;
    }
}
