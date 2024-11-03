<?php

namespace App\DataFixtures;

use App\Entity\Family;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class FamiliesFixtures extends Fixture implements DependentFixtureInterface
{
    use GeneratesFakeData;
    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();
        $faker = $this->getFaker();

        foreach ($users as $user) {
            $family = new Family();
            $family->setName('Famiglia '.$faker->lastName);
            $family->addUser($user);
            $manager->persist($family);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
