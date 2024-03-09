<?php

namespace App\Repository;

use App\Entity\Child;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ChildrenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Child::class);
    }

    public function findByFamilyId(int $familyId)
    {
        return $this->createQueryBuilder('c')
            ->where('IDENTITY(c.family) = :familyId')
            ->setParameter('familyId', $familyId)
            ->getQuery()
            ->getResult();
    }
}
