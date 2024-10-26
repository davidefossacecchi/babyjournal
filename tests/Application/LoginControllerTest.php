<?php

namespace App\Tests\Application;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginControllerTest extends WebTestCase
{

    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[action="/login"]');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('button[type="submit"]');

    }

    public function testValidLoginCredentials(): void
    {
        $client = static::createClient();

        $container = self::getContainer();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $hasher = $container->get(UserPasswordHasherInterface::class);
        $urlGenerator = $container->get(UrlGeneratorInterface::class);

        $user = new User();
        $user->setEmail('test@example.it');
        $user->setFirstName('Test');
        $user->setPassword($hasher->hashPassword($user, 'password'));
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        $client->request('GET', '/login');

        $client->submitForm('Login', [
            '_username' => 'test@example.it',
            '_password' => 'password',
        ]);

        $redirectTo = $urlGenerator->generate('family_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertResponseRedirects($redirectTo);
    }
}
