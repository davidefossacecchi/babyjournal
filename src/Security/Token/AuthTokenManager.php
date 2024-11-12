<?php

namespace App\Security\Token;

use App\Entity\AuthToken\AuthToken;
use App\Entity\User;
use App\Security\Token\Contract\AuthTokenConfiguratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class AuthTokenManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[TaggedIterator('auth_token_configurator')]
        private iterable $configurators
    ) {
    }

    public function createForUser(string $tokenFqcn, User $user): AuthToken
    {
        $configurator = $this->getConfigurator($tokenFqcn);
        $token = $configurator->configureForUser($tokenFqcn, $user);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function incrementUsage(AuthToken $token): AuthToken
    {
        $usages = $token->getUsages();
        $token->setUsages($usages + 1);
        $this->entityManager->persist($token);
        $this->entityManager->flush();

        return $token;
    }

    public function findVerified(string $tokenFqcn, string $selector, string $plainVerifier): ?AuthToken
    {
        $configurator = $this->getConfigurator($tokenFqcn);
        $token = $this->entityManager->getRepository($tokenFqcn)
            ->findOneBy(['selector' => $selector]);

        if (null === $token || !$token->isUsable()) {
            return null;
        }

        if (!$configurator->verifyToken($token, $plainVerifier)) {
            return null;
        }

        return $token;
    }

    private function getConfigurator(string $tokenFqcn): AuthTokenConfiguratorInterface
    {
        foreach ($this->configurators as $configurator) {
            if ($configurator->supports($tokenFqcn)) {
                return $configurator;
            }
        }

        throw new \LogicException(sprintf('No configurator found for token "%s"', $tokenFqcn));
    }
}
