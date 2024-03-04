<?php

namespace App\Repository;

use App\Entity\Family;
use App\Entity\Timepoints\Post;
use App\Entity\Timepoints\TimePoint;
use App\Pagination\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

class TimepointsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private int $pageLength)
    {
        parent::__construct($registry, TimePoint::class);
    }

    public function getAllByFamily(Family $family, int $page = 1)
    {
        $loadingPage = max($page, 1);
        $offset = ($loadingPage - 1) * $this->pageLength;
        $qb = $this->createQueryBuilder('tp');

        $datesCount = $qb->select($qb->expr()->countDistinct('tp.date'))
            ->where('tp INSTANCE OF '.Post::class.' AND tp.family = :family')
            ->orWhere('tp NOT INSTANCE OF '.Post::class.' AND tp.child IN (:children)')
            ->setParameter('children', $family->getChildren())
            ->setParameter('family', $family)
            ->getQuery()
            ->getSingleScalarResult();

        $dates = $this->createQueryBuilder('tp')
            ->select('tp.date')
            ->where('tp INSTANCE OF '.Post::class.' AND tp.family = :family')
            ->orWhere('tp NOT INSTANCE OF '.Post::class.' AND tp.child IN (:children)')
            ->setParameter('children', $family->getChildren())
            ->setParameter('family', $family)
            ->orderBy('tp.date', 'desc')
            ->setFirstResult($offset)
            ->setMaxResults($this->pageLength)
            ->distinct()
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_SCALAR_COLUMN);


        $records = $this->createQueryBuilder('tp')
            ->andWhere()
            ->where('tp INSTANCE OF '.Post::class.' AND tp.family = :family')
            ->orWhere('tp NOT INSTANCE OF '.Post::class.' AND tp.child IN (:children)')
            ->andWhere('tp.date IN (:dates)')
            ->setParameter('children', $family->getChildren())
            ->setParameter('family', $family)
            ->setParameter('dates', $dates)
            ->orderBy('tp.date', 'desc')
            ->getQuery()
            ->getResult();

        return new Pagination($records, $datesCount, $this->pageLength);
    }
}
