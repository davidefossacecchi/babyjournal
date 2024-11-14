<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    use GeneratesFakeData;
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function load(ObjectManager $manager): void
    {
        $faker = $this->getFaker();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setEmail($faker->email);
            $pwd = $this->passwordHasher->hashPassword($user, $faker->password);
            $user->setPassword($pwd);
            $user->setEnabled(true);
            $user->setVerified(true);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
