<?php

namespace App\Tests\Application;

use App\Entity\Family;
use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

class FamilyTest extends WebTestCase
{
    use TestFamilyAuth;
    protected KernelBrowser $client;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $this->setUpUsers();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testUserCanAccessHisFamily(): void
    {
        $urlGenerator = $this->container->get(UrlGeneratorInterface::class);

        $user = $this->getRandomUser();
        $family = $user->getFamilies()->first();
        $this->client->loginUser($user);
        $familyPostsUrl = $urlGenerator->generate('family_posts', ['id' => $family->getId()]);

        $this->client->request('GET', $familyPostsUrl);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testUserCannotAccessOtherOneFamilies(): void
    {
        $urlGenerator = $this->container->get(UrlGeneratorInterface::class);

        $user = $this->getRandomUser();
        $family = $this->getFamilyNotAssignedToUser($user);

        $this->client->loginUser($user);
        $familyPostsUrl = $urlGenerator->generate('family_posts', ['id' => $family->getId()]);

        $this->client->request('GET', $familyPostsUrl);

        $this->assertResponseStatusCodeSame(403);
    }
}
