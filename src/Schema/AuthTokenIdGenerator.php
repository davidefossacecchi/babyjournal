<?php

namespace App\Schema;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class AuthTokenIdGenerator extends AbstractIdGenerator
{
    public function generateId(EntityManagerInterface $em, $entity): string
    {
        return dechex(microtime(true) * 1000000);
    }
}
