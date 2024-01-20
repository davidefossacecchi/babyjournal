<?php

namespace App\Repository;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class PasswordResetTokenRepository extends ServiceEntityRepository
{
    private PasswordHasherInterface $hasher;
    public function __construct(ManagerRegistry $registry, PasswordHasherFactoryInterface $hasherFactory)
    {
        parent::__construct($registry, PasswordResetToken::class);
        $this->hasher = $hasherFactory->getPasswordHasher('common');
    }

    public function createForUser(User $user): PasswordResetToken
    {

        $verifier = bin2hex(random_bytes(16));
        $encryptedVerifier = $this->hasher->hash($verifier);

        $token = new PasswordResetToken();
        $token->setUser($user);
        $token->setVerifier($encryptedVerifier);
        $token->setPlainVerifier($verifier);
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

        if (false === $this->hasher->verify($token->getVerifier(), $plainVerifier)) {
            return null;
        }

        return $token;
    }
}
