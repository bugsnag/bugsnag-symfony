<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Bugsnag\BugsnagBundle\BugsnagBundle;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Callbacks\CustomUser;
use Bugsnag\Client;
use Bugsnag\Configuration as Config;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientFactory
{
    /**
     * The request resolver.
     *
     * @var \Bugsnag\BugsnagBundle\Request\SymfonyResolver
     */
    protected $resolver;

    /**
     * The token resolver.
     *
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|null
     */
    protected $tokens;

    /**
     * The auth checker.
     *
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface|null
     */
    protected $checker;

    /**
     * The api key.
     *
     * @var string|null
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
     * User detection enabled.
     *
     * @var bool
     */
    protected $user;

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
     * The symfony root.
     *
     * @var string|null
     */
    protected $root;

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
     * @param \Bugsnag\BugsnagBundle\Request\SymfonyResolver                                           $resolver
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface|null $tokens
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface|null        $checker
     * @param string|null                                                                              $key
     * @param string|null                                                                              $endpoint
     * @param bool                                                                                     $callbacks
     * @param bool                                                                                     $user
     * @param string|null                                                                              $type
     * @param string|null                                                                              $version
     * @param bool                                                                                     $batch
     * @param string|null                                                                              $hostname
     * @param bool                                                                                     $code
     * @param string|null                                                                              $strip
     * @param string|null                                                                              $project
     * @param string|null                                                                              $root
     * @param string|null                                                                              $stage
     * @param string[]|null                                                                            $stages
     * @param string[]|null                                                                            $filters
     *
     * @return void
     */
    public function __construct(
        SymfonyResolver $resolver,
        TokenStorageInterface $tokens = null,
        AuthorizationCheckerInterface $checker = null,
        $key = null,
        $endpoint = null,
        $callbacks = true,
        $user = true,
        $type = null,
        $version = true,
        $batch = null,
        $hostname = null,
        $code = true,
        $strip = null,
        $project = null,
        $root = null,
        $stage = null,
        array $stages = null,
        array $filters = null
    ) {
        $this->resolver = $resolver;
        $this->tokens = $tokens;
        $this->checker = $checker;
        $this->key = $key;
        $this->endpoint = $endpoint;
        $this->callbacks = $callbacks;
        $this->user = $user;
        $this->type = $type;
        $this->version = $version;
        $this->batch = $batch;
        $this->hostname = $hostname;
        $this->code = $code;
        $this->strip = $strip;
        $this->project = $project;
        $this->root = $root;
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

        $client = new Client(new Config($this->key ?: ''), $this->resolver, $guzzle);

        if ($this->callbacks) {
            $client->registerDefaultCallbacks();
        }

        if ($this->tokens && $this->checker && $this->user) {
            $this->setupUserDetection($client, $this->tokens, $this->checker);
        }

        $this->setupPaths($client, $this->strip, $this->project, $this->root);

        $client->setReleaseStage($this->stage === 'prod' ? 'production' : $this->stage);

        $client->setAppVersion($this->version);
        $client->setFallbackType('Console');
        $client->setAppType($this->type);

        $client->setBatchSending($this->batch);
        $client->setHostname($this->hostname);
        $client->setSendCode($this->code);

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

    /**
     * Setup user detection.
     *
     * @param \Bugsnag\Client                                                                     $client
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokens
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface        $checker
     *
     * @return void
     */
    protected function setupUserDetection(Client $client, TokenStorageInterface $tokens, AuthorizationCheckerInterface $checker)
    {
        $client->registerCallback(new CustomUser(function () use ($tokens, $checker) {
            $token = $tokens->getToken();

            if (!$token || !$checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                return;
            }

            $user = $token->getUser();

            if ($user instanceof UserInterface) {
                return ['id' => $user->getUsername()];
            }

            return ['id' => (string) $user];
        }));
    }

    /**
     * Setup the client paths.
     *
     * @param \Bugsnag\Client $client
     * @param string|null     $strip
     * @param string|null     $project
     * @param string|null     $root
     *
     * @return void
     */
    protected function setupPaths(Client $client, $strip, $project, $root)
    {
        if ($strip) {
            $client->setStripPath($strip);

            if (!$project) {
                $client->setProjectRoot("{$strip}/src");
            }

            return;
        }

        $base = $root ? realpath("{$root}/../") : false;

        if ($project) {
            if ($base && substr($project, 0, strlen($base)) === $base) {
                $client->setStripPath($base);
            }

            $client->setProjectRoot($project);

            return;
        }

        if ($base) {
            $client->setStripPath($base);
            $client->setProjectRoot("{$base}/src");
        }
    }
}
