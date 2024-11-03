<?php

namespace App\DataFixtures;

use App\Entity\Child;
use App\Entity\Family;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ChildrenFixtures extends Fixture implements DependentFixtureInterface
{
    use GeneratesFakeData;
    public function load(ObjectManager $manager): void
    {
        $families = $manager->getRepository(Family::class)->findAll();
        $faker = $this->getFaker();
        foreach ($families as $family) {
            $childrenCount = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $childrenCount; $i++) {
                $child = new Child();
                $child->setName($faker->firstName);
                $bd = $faker->dateTimeBetween('-10 years', '-1 years');
                $child->setBirthDate(DateTimeImmutable::createFromInterface($bd));
                $child->setFamily($family);
                $manager->persist($child);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FamiliesFixtures::class,
        ];
    }
}
