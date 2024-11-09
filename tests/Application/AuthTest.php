<?php

namespace App\Tests\Application;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthTest extends WebTestCase
{
    public function testPasswordRecoveryLinkIsCorrectlySent(): void
    {
        $client = static::createClient();
        $container = self::getContainer();

        $router = $container->get(UrlGeneratorInterface::class);
        $em = $container->get(EntityManagerInterface::class);


        $user = $em->getRepository(User::class)
            ->findAll()[0];

        $passwordResetUrl = $router->generate('password_reset');
        $client->request('GET', $passwordResetUrl);

        $crawler = $client->submitForm('Recupera Password', [
            'form[email]' => $user->getEmail()
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEmailCount(1);

        $email = $this->getMailerMessage(0);
        $this->assertEmailHeaderSame($email, 'To', $user->getEmail());

        $tokens = $em->getRepository(PasswordResetToken::class)
            ->findBy(['user' => $user]);
        $this->assertEmailHtmlBodyContains($email, $tokens[0]->getSelector().'.');
    }
}
