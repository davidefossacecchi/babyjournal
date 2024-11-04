<?php

namespace App\Tests\Application;

use App\Entity\Timepoints\Height;
use App\Twig\Components\ChildTimepointForm;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;

class TimepointTest extends WebTestCase
{
    use InteractsWithLiveComponents;
    use TestFamilyAuth;

    protected KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->container = static::getContainer();
        $this->setUpUsers();
    }

    public function testUserCanSubmitTimepoints(): void
    {
        $session = new Session(new MockFileSessionStorage());
        $request = new Request();
        $request->setSession($session);
        $stack = $this->container->get(RequestStack::class);
        $stack->push($request);
        $user = $this->getRandomUser();
        $family = $user->getFamilies()->first();
        $child = $family->getChildren()->first();
        $this->client->loginUser($user);

        $liveComponent = $this->createLiveComponent(
            ChildTimepointForm::class,
            [
                'familyId' => $family->getId(),
                'timepointType' => 'height'
            ],
            $this->client
        );

        $liveComponent
            ->actingAs($user)
            ->submitForm(['timepoint' => [
                'child' => $child->getId(),
                'value' => 100,
                'date' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]], 'save');


        $this->assertResponseIsSuccessful();
        $timepoint = $child->getTimepoints()->last();
        $this->assertEquals(100, $timepoint->getValue());
        $this->assertInstanceOf(Height::class, $timepoint);

    }
}
