<?php

namespace Bugsnag\BugsnagBundle\Tests\Listener;

use Bugsnag\BugsnagBundle\EventListener\BugsnagShutdown;
use Bugsnag\Client;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BugsnagShutdownTest extends TestCase
{
    use MockeryTrait;

    public function testEventsBeingSubscribedTo()
    {
        $events = BugsnagShutdown::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::TERMINATE, $events);
        $this->assertArrayHasKey(ConsoleEvents::TERMINATE, $events);

        foreach ($events as $event) {
            $this->assertEquals('onTerminate', $event[0]);
        }
    }

    public function testOnTerminate()
    {
        // Assert that Client::flush() is called
        $mockClient = \Mockery::mock(Client::class);
        $mockClient->shouldReceive('flush')->once();

        $shutdown = new BugsnagShutdown();
        $shutdown->registerShutdownStrategy($mockClient);
        $shutdown->onTerminate(\Mockery::mock(TerminateEvent::class));

        $this->tearDownMockery();
    }
}
