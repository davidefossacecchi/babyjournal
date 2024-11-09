<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use App\Security\Token\AuthTokenManager;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordResetTokenRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry, private readonly AuthTokenManager $authTokenManager)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function createForUser(User $user): PasswordResetToken
    {
        $token = new PasswordResetToken();
        $token->setUser($user);
        $token = $this->authTokenManager->configureAuthToken($token);
        $token->setUsages(0);
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }

    public function incrementUsage(PasswordResetToken $token): PasswordResetToken
    {
        $usages = $token->getUsages();
        $token->setUsages($usages + 1);
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();

        return $token;
    }

    public function findVerified(string $selector, string $plainVerifier): ?PasswordResetToken
    {
        /** @var PasswordResetToken|null $token */
        $token = $this->findOneBy(['selector' => $selector]);

        if (empty($token) || false === $token->isUsable()) {
            return null;
        }

        if (false === $this->authTokenManager->verifyAuthToken($token, $plainVerifier)) {
            return null;
        }

        return $token;
    }
}
