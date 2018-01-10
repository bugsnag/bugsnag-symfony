<?php

namespace Bugsnag\BugsnagBundle\EventListener;

use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Report;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class BugsnagListener
{
    /**
     * The bugsnag client instance.
     *
     * @var \Bugsnag\Client
     */
    protected $client;

    /**
     * The request resolver instance.
     *
     * @var \Bugsnag\BugsnagBundle\Request\SymfonyResolver
     */
    protected $resolver;

    /**
     * If auto notifying is enabled.
     *
     * @var bool
     */
    protected $auto;

    /**
     * Create a new bugsnag listener instance.
     *
     * @param \Bugsnag\Client                                $client
     * @param \Bugsnag\BugsnagBundle\Request\SymfonyResolver $resolver
     * @param bool                                           $auto
     *
     * @return void
     */
    public function __construct(Client $client, SymfonyResolver $resolver, $auto)
    {
        $this->client = $client;
        $this->resolver = $resolver;
        $this->auto = $auto;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if ($this->client->shouldCaptureSessions()) {
            $this->client->startSession();
        }

        $this->client->setFallbackType('HTTP');

        $this->resolver->set($event->getRequest());
    }

    /**
     * Handle an http kernel exception.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->auto) {
            return;
        }

        $exception = $event->getException();

        $report = Report::fromPHPThrowable(
            $this->client->getConfig(),
            $exception
        );
        $report->setUnhandled(true);
        $report->setSeverityReason([
            'type' => 'unhandledExceptionMiddleware',
            'attributes' => [
                'framework' => 'Symfony',
            ],
        ]);

        $this->client->notify($report);
    }

    /**
     * Handle a console exception.
     *
     * @param \Symfony\Component\Console\Event\ConsoleExceptionEvent $event
     *
     * @return void
     */
    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        if (!$this->auto) {
            return;
        }

        $exception = $event->getException();

        $meta = [
            'command' => [
                'name' => $event->getCommand()->getName(),
                'status' => $event->getExitCode(),
            ],
        ];

        $report = Report::fromPHPThrowable(
            $this->client->getConfig(),
            $exception
        );
        $report->setUnhandled(true);
        $report->setSeverityReason([
            'type' => 'unhandledExceptionMiddleware',
            'attributes' => [
                'framework' => 'Symfony',
            ],
        ]);
        $report->setMetaData($meta);

        $this->client->notify($report);
    }
}
