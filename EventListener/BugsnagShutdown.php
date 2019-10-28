<?php

namespace Bugsnag\BugsnagBundle\EventListener;

use Bugsnag\Client;
use Bugsnag\Shutdown\ShutdownStrategyInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BugsnagShutdown implements EventSubscriberInterface, ShutdownStrategyInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param TerminateEvent $event
     */
    public function onTerminate(TerminateEvent $event)
    {
        if ($this->client) {
            file_put_contents('/tmp/rtest', $event->getRequest()->getUri()."\n");
            $this->client->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        $listeners = [
            KernelEvents::TERMINATE => ['onTerminate', 10],
        ];

        // Added ConsoleEvents in Symfony 2.3
        if (class_exists('Symfony\Component\Console\ConsoleEvents')) {
            $listeners[ConsoleEvents::TERMINATE] = ['onTerminate', 10];
        }

        return $listeners;
    }

    public function register(Client $client)
    {
        $this->client = $client;
    }
}
