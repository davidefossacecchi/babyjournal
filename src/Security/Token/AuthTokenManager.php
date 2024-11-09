<?php

namespace App\Security\Token;

use App\Entity\AuthToken;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AuthTokenManager
{
    private readonly PasswordHasherInterface $hasher;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasher = $hasherFactory->getPasswordHasher('common');
    }

    public function configureAuthToken(AuthToken $token): AuthToken
    {
        $selector = bin2hex(random_bytes(16));
        $verifier = bin2hex(random_bytes(16));
        $encryptedVerifier = $this->hasher->hash($verifier);

        $token->setSelector($selector);
        $token->setVerifier($encryptedVerifier);
        $token->setPlainVerifier($verifier);

        return $token;
    }

    public function verifyAuthToken(AuthToken $token, string $plainVerifier): bool
    {
        return $this->hasher->verify($token->getVerifier(), $plainVerifier);
    }
}
