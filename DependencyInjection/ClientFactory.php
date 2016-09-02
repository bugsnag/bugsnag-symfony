<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Bugsnag\BugsnagBundle\BugsnagBundle;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Bugsnag\Configuration as Config;

class ClientFactory
{
    /**
     * The request resolver.
     *
     * @var \Bugsnag\BugsnagBundle\Request\SymfonyResolver
     */
    protected $resolver;

    /**
     * The api key.
     *
     * @var string
     */
    protected $key;

    /**
     * The endpoint.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * The callbacks.
     *
     * @var bool
     */
    protected $callbacks;

    /**
     * The type.
     *
     * @var string|null
     */
    protected $type;

    /**
     * The version.
     *
     * @var string|null
     */
    protected $version;

    /**
     * The batch sending.
     *
     * @var bool
     */
    protected $batch;

    /**
     * The hostname.
     *
     * @var string|null
     */
    protected $hostname;

    /**
     * The send code flag.
     *
     * @var bool
     */
    protected $code;

    /**
     * The strip path.
     *
     * @var string|null
     */
    protected $strip;

    /**
     * The project root.
     *
     * @var string|null
     */
    protected $project;

    /**
     * The release stage.
     *
     * @var string|null
     */
    protected $stage;

    /**
     * The notify release stages.
     *
     * @var string[]|null
     */
    protected $stages;

    /**
     * The filters.
     *
     * @var string[]|null
     */
    protected $filters;

    /**
     * Create a new client factory instance.
     *
     * @param \Bugsnag\BugsnagBundle\Request\SymfonyResolver $resolver
     * @param string                                         $key
     * @param string|null                                    $endpoint
     * @param bool                                           $callbacks
     * @param string|null                                    $type
     * @param string|null                                    $version
     * @param bool                                           $batch
     * @param string|null                                    $hostname
     * @param bool                                           $code
     * @param string|null                                    $strip
     * @param string|null                                    $project
     * @param string|null                                    $stage
     * @param string[]|null                                  $stages
     * @param string[]|null                                  $filters
     *
     * @return void
     */
    public function __construct(
        SymfonyResolver $resolver,
        $key,
        $endpoint = null,
        $callbacks = true,
        $type = null,
        $version = true,
        $batch = null,
        $hostname = null,
        $code = true,
        $strip = null,
        $project = null,
        $stage = null,
        array $stages = null,
        array $filters = null
    ) {
        $this->resolver = $resolver;
        $this->key = $key;
        $this->endpoint = $endpoint;
        $this->callbacks = $callbacks;
        $this->type = $type;
        $this->version = $version;
        $this->batch = $batch;
        $this->hostname = $hostname;
        $this->code = $code;
        $this->strip = $strip;
        $this->project = $project;
        $this->stage = $stage;
        $this->stages = $stages;
        $this->filters = $filters;
    }

    /**
     * Make a new client instance.
     *
     * @return \Bugsnag\Client
     */
    public function make()
    {
        $guzzle = Client::makeGuzzle($this->endpoint);

        $client = new Client(new Config($this->key), $this->resolver, $guzzle);

        if ($this->callbacks) {
            $client->registerDefaultCallbacks();
        }

        if ($this->strip) {
            $client->setStripPath($this->strip);

            if (!$this->project) {
                $client->setProjectRoot("{$this->strip}/src");
            }
        } elseif ($this->project) {
            $client->setProjectRoot($this->project);
        }

        $client->setReleaseStage($this->stage === 'prod' ? 'production' : $this->stage);

        $client->setNotifier(array_filter([
            'name' => 'Bugsnag Symfony',
            'version' => BugsnagBundle::VERSION,
            'url' => 'https://github.com/bugsnag/bugsnag-symfony',
        ]));

        if ($this->stages) {
            $client->setNotifyReleaseStages($this->stages);
        }

        if ($this->filters) {
            $client->setFilters($this->filters);
        }

        return $client;
    }
}
