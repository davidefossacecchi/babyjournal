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
    protected array $users;
    protected ContainerInterface $container;
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
        $objectManager = $this->container->get(EntityManagerInterface::class);
        /** @var UserRepository $userRepo */
        $userRepo = $objectManager->getRepository(User::class);

        $this->users = $userRepo->findAll();
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
        $family = $user->getFamilies();
        $familyIds = $family->map(fn($f) => $f->getId())->toArray();
        $familyRepo = $this->container->get(EntityManagerInterface::class)
            ->getRepository(Family::class);

        $qb = $familyRepo->createQueryBuilder('f');
        $family = $qb->where($qb->expr()->notIn('f.id', $familyIds))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $this->client->loginUser($user);
        $familyPostsUrl = $urlGenerator->generate('family_posts', ['id' => $family->getId()]);

        $this->client->request('GET', $familyPostsUrl);

        $this->assertResponseStatusCodeSame(403);
    }

    protected function getRandomUser(): User
    {
        $max = count($this->users) - 1;
        $randomIndex = rand(0, $max);
        return $this->users[$randomIndex];
    }
}
