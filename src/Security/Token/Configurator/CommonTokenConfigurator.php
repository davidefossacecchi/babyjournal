<?php

namespace App\Security\Token\Configurator;

use App\Entity\AuthToken\AuthToken;
use App\Entity\AuthToken\EmailVerificationToken;
use App\Entity\AuthToken\FamilyInvitationToken;
use App\Entity\AuthToken\PasswordResetToken;
use App\Entity\User;
use App\Security\Token\Contract\AuthTokenConfiguratorInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class CommonTokenConfigurator implements AuthTokenConfiguratorInterface
{
    use GeneratesRandomHex;
    private readonly PasswordHasherInterface $hasher;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasher = $hasherFactory->getPasswordHasher('common');
    }
    public function configureForUser(string $tokenFqcn, User $user): AuthToken
    {

        $selector = $this->getRandomHex();
        $verifier = $this->getRandomHex();

        $encryptedVerifier = $this->hasher->hash($verifier);

        /** @var AuthToken $token */
        $token = new $tokenFqcn();
        $token->setSelector($selector);
        $token->setVerifier($encryptedVerifier);
        $token->setPlainVerifier($verifier);
        $token->setUser($user);
        $token->setUsages(0);
        return $token;
    }

    public function verifyToken(AuthToken $token, string $plainVerifier): bool
    {
        return $this->hasher->verify($token->getVerifier(), $plainVerifier);
    }

    public function supports(string $tokenFqcn): bool
    {
        return in_array($tokenFqcn, [
            PasswordResetToken::class,
            EmailVerificationToken::class,
            FamilyInvitationToken::class
        ]);
    }

}
