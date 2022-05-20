<?php

namespace AppBundle\EventSubscriber;

use Bugsnag\Client;
use Bugsnag\FeatureFlag;
use Bugsnag\Report;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class AddFeatureFlagsOnRequest implements EventSubscriberInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function onKernelRequest()
    {
        $this->client->addFeatureFlags([
            new FeatureFlag('added at runtime 1'),
            new FeatureFlag('added at runtime 2', 'runtime_2'),
            new FeatureFlag('added at runtime 3', 'to be removed'),
            new FeatureFlag('added at runtime 4'),
        ]);

        $this->client->clearFeatureFlag('added at runtime 3');

        $this->client->registerCallback(function (Report $report) {
            $report->addFeatureFlags([
                new FeatureFlag('from global on error 1', 'on error 1'),
                new FeatureFlag('from global on error 2'),
                new FeatureFlag('from global on error 3', '111'),
                new FeatureFlag('from global on error 4'),
            ]);
        });

        $this->client->registerCallback(function (Report $report) {
            $report->clearFeatureFlag('from global on error 4');
        });
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 256],
        ];
    }
}
