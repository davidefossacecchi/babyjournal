<?php

namespace App\Repository;

use App\Entity\Family;
use App\Entity\Timepoints\Post;
use App\Entity\Timepoints\TimePoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TimepointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimePoint::class);
    }

    public function getAllByFamily(Family $family)
    {
        return $this->createQueryBuilder('tp')
            ->where('tp INSTANCE OF '.Post::class.' AND tp.family = :family')
            ->orWhere('tp NOT INSTANCE OF '.Post::class.' AND tp.child IN (:children)')
            ->setParameter('children', $family->getChildren())
            ->setParameter('family', $family)
            ->getQuery()
            ->getResult();
    }
}
