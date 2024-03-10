<?php

namespace App\EventListener;

use App\Entity\Child;
use App\Entity\Timepoints\Birthday;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist)]
class DefaultTimepointsPersister
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {

    }

    public function postPersist(PostPersistEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        switch (true) {
            case $entity instanceof Child:
                $birthday = new Birthday();
                $birthday->setChild($entity);
                $birthday->setDate($entity->getBirthDate());
                $this->entityManager->persist($birthday);
                $this->entityManager->flush();
                break;
        }
    }
}
