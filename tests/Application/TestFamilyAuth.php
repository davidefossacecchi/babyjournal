<?php

namespace App\Tests\Application;

use App\Entity\Family;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

trait TestFamilyAuth
{
    protected array $users;
    protected ContainerInterface $container;

    protected function setUpUsers(): void
    {
        $objectManager = $this->container->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepo */
        $userRepo = $objectManager->getRepository(User::class);
        $this->users = $userRepo->findAll();
    }

    protected function getRandomUser(): User
    {
        $i = array_rand($this->users);
        return $this->users[$i];
    }

    protected function getFamilyNotAssignedToUser(User $user): Family
    {
        $families = $user->getFamilies();
        $familyIds = $families->map(fn($f) => $f->getId())->toArray();
        $familyRepo = $this->container->get(EntityManagerInterface::class)
            ->getRepository(Family::class);

        $qb = $familyRepo->createQueryBuilder('f');
        /** @var Family $family*/
        $family = $qb->where($qb->expr()->notIn('f.id', $familyIds))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $family;
    }
}
