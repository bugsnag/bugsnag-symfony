<?php

namespace Bugsnag\BugsnagBundle\EventListener;

use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Report;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class BugsnagListener implements EventSubscriberInterface
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
        $this->sendNotify($event->getException(), []);
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
        $meta = [ 'status' => $event->getExitCode() ];
        if ($event->getCommand()) {
            $meta['name'] = $event->getCommand()->getName();
        }
        $this->sendNotify($event->getException(), ['command' => $meta]);
    }

    /**
     * Handle a console error.
     *
     * @param \Symfony\Component\Console\Event\ConsoleErrorEvent $event
     *
     * @return void
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $meta = [ 'status' => $event->getExitCode() ];
        if ($event->getCommand()) {
            $meta['name'] = $event->getCommand()->getName();
        }
        $this->sendNotify($event->getError(), ['command' => $meta]);
    }

    private function sendNotify($throwable, $meta)
    {
        if (!$this->auto) {
            return;
        }

        $report = Report::fromPHPThrowable(
            $this->client->getConfig(),
            $throwable
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

    public static function getSubscribedEvents(){
        $listeners = [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
            KernelEvents::EXCEPTION => ['onKernelException', 128]
        ];

        // Added ConsoleEvents in Symfony 2.3
        if (class_exists('Symfony\Component\Console\ConsoleEvents')) {
            // Added with ConsoleEvents::ERROR in Symfony 3.3 to deprecate ConsoleEvents::EXCEPTION
            if (class_exists('Symfony\Component\Console\Event\ConsoleErrorEvent')) {
                $listeners[ConsoleEvents::ERROR] = ['onConsoleError', 128];
            } else {
                $listeners[ConsoleEvents::EXCEPTION] = ['onConsoleException', 128];
            }
        }

        return $listeners;
    }
}
