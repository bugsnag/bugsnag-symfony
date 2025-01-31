<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Bugsnag\BugsnagBundle\BugsnagBundle;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Callbacks\CustomUser;
use Bugsnag\Client;
use Bugsnag\Configuration as Config;
use Bugsnag\FeatureFlag;
use Bugsnag\Shutdown\ShutdownStrategyInterface;
use GuzzleHttp;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * Note this won't be used if 'stripPathRegex' is also given.
     *
     * @var string|null
     */
    protected $strip;

    /**
     * The project root.
     *
     * Note this won't be used if 'projectRootRegex' is also given.
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
     * The environment name.
     *
     * @var string|null
     */
    protected $env;

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
     * @deprecated use redactedKeys instead
     *
     * @var string[]|null
     */
    protected $filters;

    /**
     * @var \Bugsnag\Shutdown\ShutdownStrategyInterface
     */
    protected $shutdownStrategy;

    /**
     * The strip path as a regular expression.
     *
     * This takes precedence over 'strip' if both are given.
     *
     * @var string|null
     */
    protected $stripPathRegex;

    /**
     * The project root as a regular expression.
     *
     * This takes precedence over 'project' if both are given.
     *
     * @var string|null
     */
    protected $projectRootRegex;

    /**
     * The Guzzle client Bugsnag will use.
     *
     * @var GuzzleHttp\ClientInterface
     */
    private $guzzle;

    /**
     * The amount to increase the memory_limit to handle an OOM.
     *
     * This can be disabled by setting "bugsnag.memory_limit_increase" to "null"
     *
     * @var int|null|false
     */
    private $memoryLimitIncrease;

    /**
     * An array of classes that should not be sent to Bugsnag.
     *
     * This can contain both fully qualified class names and regular expressions.
     *
     * @var array
     */
    private $discardClasses;

    /**
     * An array of metadata keys that should be redacted.
     *
     * @var string[]
     */
    private $redactedKeys;

    /**
     * An array of feature flags with a "name" and optional "variant".
     *
     * For example:
     * [['name' => 'example', 'variant' => 'test'], ['name' => 'another name']]
     *
     * @var array[]
     */
    private $featureFlags;

    /**
     * The maximum number of breadcrumbs that are allowed to be stored.
     *
     * @var int|null
     */
    private $maxBreadcrumbs;

    /**
     * @param SymfonyResolver                    $resolver
     * @param TokenStorageInterface|null         $tokens
     * @param AuthorizationCheckerInterface|null $checker
     * @param string|null                        $key
     * @param string|null                        $endpoint
     * @param bool                               $callbacks
     * @param bool                               $user
     * @param string|null                        $type
     * @param string|null                        $version
     * @param bool                               $batch
     * @param string|null                        $hostname
     * @param bool                               $code
     * @param string|null                        $strip
     * @param string|null                        $project
     * @param string|null                        $root
     * @param string|null                        $env
     * @param string|null                        $stage
     * @param string[]|null                      $stages
     * @param string[]|null                      $filters
     * @param ShutdownStrategyInterface          $shutdownStrategy
     * @param string|null                        $stripPathRegex
     * @param string|null                        $projectRootRegex
     * @param GuzzleHttp\ClientInterface|null    $guzzle
     * @param int|null|false                     $memoryLimitIncrease
     * @param array                              $discardClasses
     * @param string[]                           $redactedKeys
     * @param array[]                            $featureFlags
     * @param int|null                           $maxBreadcrumbs
     *
     * @return void
     */
    public function __construct(
        SymfonyResolver $resolver,
        $tokens = null,
        $checker = null,
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
        $env = null,
        $stage = null,
        $stages = null,
        $filters = null,
        $shutdownStrategy = null,
        $stripPathRegex = null,
        $projectRootRegex = null,
        $guzzle = null,
        $memoryLimitIncrease = false,
        array $discardClasses = [],
        array $redactedKeys = [],
        array $featureFlags = [],
        $maxBreadcrumbs = null
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
        $this->env = $env;
        $this->stage = $stage;
        $this->stages = $stages;
        $this->filters = $filters;
        $this->shutdownStrategy = $shutdownStrategy;
        $this->stripPathRegex = $stripPathRegex;
        $this->projectRootRegex = $projectRootRegex;
        $this->guzzle = $guzzle === null
            ? Client::makeGuzzle()
            : $guzzle;
        $this->memoryLimitIncrease = $memoryLimitIncrease;
        $this->discardClasses = $discardClasses;
        $this->redactedKeys = $redactedKeys;
        $this->featureFlags = $featureFlags;
        $this->maxBreadcrumbs = $maxBreadcrumbs;
    }

    /**
     * Make a new client instance.
     *
     * @return \Bugsnag\Client
     */
    public function make()
    {
        $client = new Client(
            new Config($this->key ?: ''),
            $this->resolver,
            $this->guzzle,
            $this->shutdownStrategy
        );

        if ($this->callbacks) {
            $client->registerDefaultCallbacks();
        }

        if ($this->tokens && $this->checker && $this->user) {
            $this->setupUserDetection($client, $this->tokens, $this->checker);
        }

        $this->setupPaths($client);

        $client->setReleaseStage($this->stage ?: ($this->env === 'prod' ? 'production' : $this->env));

        $client->setAppVersion($this->version);
        $client->setFallbackType('Console');
        $client->setAppType($this->type);

        $client->setBatchSending($this->batch);
        $client->setHostname($this->hostname);
        $client->setSendCode($this->code);

        $client->getConfig()->mergeDeviceData(['runtimeVersions' => ['symfony' => Kernel::VERSION]]);

        $client->setNotifier([
            'name' => 'Bugsnag Symfony',
            'version' => BugsnagBundle::VERSION,
            'url' => 'https://github.com/bugsnag/bugsnag-symfony',
        ]);

        if ($this->endpoint !== null) {
            $client->setNotifyEndpoint($this->endpoint);
        }

        if ($this->stages) {
            $client->setNotifyReleaseStages($this->stages);
        }

        if ($this->filters) {
            $client->setFilters($this->filters);
        }

        // "false" is used as a sentinel here because "null" is a valid value
        if ($this->memoryLimitIncrease !== false) {
            $client->setMemoryLimitIncrease($this->memoryLimitIncrease);
        }

        if ($this->discardClasses) {
            $client->setDiscardClasses($this->discardClasses);
        }

        if ($this->redactedKeys) {
            $client->setRedactedKeys($this->redactedKeys);
        }

        if ($this->featureFlags) {
            $featureFlags = array_map(function (array $flag) {
                if (array_key_exists('variant', $flag)) {
                    return new FeatureFlag($flag['name'], $flag['variant']);
                }

                return new FeatureFlag($flag['name']);
            }, $this->featureFlags);

            $client->addFeatureFlags($featureFlags);
        }

        if ($this->maxBreadcrumbs !== null) {
            $client->setMaxBreadcrumbs($this->maxBreadcrumbs);
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
                // Symfony 5.3 introduced 'getUserIdentifier' to replace 'getUsername'
                // Symfony 6.0 removed 'getUsername'
                if (method_exists(UserInterface::class, 'getUserIdentifier')) {
                    return ['id' => $user->getUserIdentifier()];
                }

                // Symfony 5.4 and below use 'getUsername' ('getUserIdentifier'
                // wasn't added to the interface until Symfony 6.0 for BC)
                if (method_exists(UserInterface::class, 'getUsername')) {
                    return ['id' => $user->getUsername()];
                }

                // if neither method exists then we don't know how to deal with
                // this version of Symfony's UserInterface, so can't get an ID
                return;
            }

            return ['id' => (string) $user];
        }));
    }

    /**
     * Setup the client paths.
     *
     * @param Client $client
     *
     * @return void
     */
    protected function setupPaths(Client $client)
    {
        if ($this->projectRootRegex !== null) {
            $client->setProjectRootRegex($this->projectRootRegex);
        } elseif ($this->project !== null) {
            $client->setProjectRoot($this->project);
        } else {
            $client->setProjectRoot($this->root.DIRECTORY_SEPARATOR.'src');
        }

        if ($this->stripPathRegex !== null) {
            $client->setStripPathRegex($this->stripPathRegex);
        } elseif ($this->strip !== null) {
            $client->setStripPath($this->strip);
        } else {
            $client->setStripPath($this->root);
        }
    }
}
