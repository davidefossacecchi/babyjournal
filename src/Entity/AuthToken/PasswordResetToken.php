<?php

namespace App\Entity\AuthToken;
use App\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PasswordResetToken extends AuthToken
{
    private const MAX_USAGES = 1;
    public function hasReachedMaxUsages(): bool
    {
        return self::MAX_USAGES < $this->getUsages();
    }

    public function getTTL(): \DateInterval
    {
        return new \DateInterval('P1D');
    }
}
