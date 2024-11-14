<?php

namespace App\Security\Token\Contract;

use App\Entity\AuthToken\AuthToken;
use App\Entity\User;

interface AuthTokenConfiguratorInterface
{
    public function configureForUser(string $tokenFqcn, User $user): AuthToken;

    public function verifyToken(AuthToken $token, string $plainVerifier): bool;

    public function supports(string $tokenFqcn): bool;
}
