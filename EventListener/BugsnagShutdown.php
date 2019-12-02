<?php

namespace Bugsnag\BugsnagBundle\EventListener;

use Bugsnag\Client;
use Bugsnag\Shutdown\ShutdownStrategyInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * A Shutdown strategy that uses Symfony's TERMINATE event to trigger calls to Client::flush().
 *
 * This is preferred to the default PhpShutdownStrategy for two primary reasons:
 *
 * 1) No memory leaks When running tests: memory used by the Bugsnag\Client can be garbage collected (this is not
 *    possible when register_shutdown_function is used).
 *
 * 2) Better performance: Symfony uses fastcgi_finish_request(), so if PHP-FPM is being used flush() calls take place
 *    after the HTTP connection has closed.
 */
class BugsnagShutdown implements EventSubscriberInterface, ShutdownStrategyInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Indicate which events we wish to subscribe to.
     *
     * @return array
     */
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

    /**
     * Called when Symfony shuts down the kernel (after response has been sent).
     *
     * @return void
     */
    public function onTerminate()
    {
        if ($this->client) {
            $this->client->flush();
        }
    }

    /**
     * Implement the ShutdownStrategyInterface.
     *
     * @param \Bugsnag\Client $client
     *
     * @return void
     */
    public function registerShutdownStrategy(Client $client)
    {
        /*
         * Set a reference to the client. This "enables" the onTerminate event.
         */
        $this->client = $client;
    }
}
