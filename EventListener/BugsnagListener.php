<?php

namespace Bugsnag\BugsnagBundle\EventListener;

use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
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

    protected $clientFactory;

    /**
     * BugsnagListener constructor.
     * @param Client $client
     * @param SymfonyResolver $resolver
     * @param $auto
     * @param ClientFactory $clientFactory
     */
    public function __construct(Client $client, SymfonyResolver $resolver, $auto, ClientFactory $clientFactory)
    {

        $this->client = $client;
        $this->resolver = $resolver;
        $this->auto = $auto;
        $this->clientFactory = $clientFactory;
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

        if (!$this->auto) {
            return;
        }

        $exception = $event->getException();

        $this->notifyException($exception);
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

        $this->notifyException($exception, $meta);
    }


    private function notifyException($exception, $meta = null)
    {
        $excepts = $this->clientFactory->getExcepts();

        if (in_array(get_class($exception), $excepts)) {
            return;
        }

        if (empty($meta)) {
            $this->client->notifyException($exception);
        } else {
            $this->client->notifyException($exception, function (Report $report) use ($meta) {
                $report->setMetaData($meta);
            });
        }
    }
}
