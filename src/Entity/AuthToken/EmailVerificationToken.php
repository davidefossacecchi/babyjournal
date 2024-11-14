<?php

namespace App\Entity\AuthToken;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EmailVerificationToken extends AuthToken
{
    public function hasReachedMaxUsages(): bool
    {
        return $this->getUsages() >= 1;
    }

    public function getTTL(): \DateInterval
    {
        return new \DateInterval('P1D');
    }
}
