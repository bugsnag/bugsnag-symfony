<?php

namespace Bugsnag\BugsnagBundle\Tests\DependencyInjection;

use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\BugsnagBundle\DependencyInjection\Configuration;
use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\EventListener\BugsnagShutdown;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class ConfigurationTest extends TestCase
{
    private $defaultConfiguration = [
        'api_key' => null,
        'endpoint' => null,
        'callbacks' => true,
        'user' => true,
        'app_type' => null,
        'app_version' => null,
        'batch_sending' => true,
        'hostname' => null,
        'send_code' => true,
        'release_stage' => null,
        'strip_path' => null,
        'project_root' => null,
        'auto_notify' => true,
        'resolver' => SymfonyResolver::class,
        'factory' => ClientFactory::class,
        'client' => Client::class,
        'listener' => BugsnagListener::class,
        'notify_release_stages' => [],
        'filters' => [],
        'shutdown' => BugsnagShutdown::class,
        'strip_path_regex' => null,
        'project_root_regex' => null,
        'guzzle' => null,
        'memory_limit_increase' => false,
        'discard_classes' => [],
        'redacted_keys' => [],
        'feature_flags' => [],
        'max_breadcrumbs' => null,
    ];

    /**
     * @dataProvider configProvider
     *
     * @param array $input
     * @param array $expected
     *
     * @return void
     */
    public function testConfiguration(array $input, array $expected)
    {
        $actual = $this->processConfiguration($input);

        $this->assertSame($expected, $actual);
    }

    public function configProvider()
    {
        return [
            'no provided config' => [
                [],
                $this->buildExpectedConfiguration(),
            ],
            'some provided config' => [
                $someConfig = [
                    'api_key' => 'key123',
                    'callbacks' => false,
                    'send_code' => false,
                    'notify_release_stages' => ['one', 'two', 'three'],
                    'strip_path_regex' => '/abc/',
                    'memory_limit_increase' => 1234,
                ],
                $this->buildExpectedConfiguration($someConfig),
            ],
            'all provided config' => [
                $fullConfig = [
                    'api_key' => 'my api key',
                    'endpoint' => 'https://example.com',
                    'callbacks' => false,
                    'user' => false,
                    'app_type' => 'good',
                    'app_version' => '1.2.3',
                    'batch_sending' => false,
                    'hostname' => 'example.com',
                    'send_code' => false,
                    'release_stage' => 'staging',
                    'strip_path' => '/a/b/c',
                    'project_root' => '/x/y/z',
                    'auto_notify' => false,
                    'resolver' => 'MyResolver',
                    'factory' => 'MyFactory',
                    'client' => 'MyClient',
                    'listener' => 'MyListener',
                    'notify_release_stages' => ['not staging'],
                    'filters' => ['a', 'b', 'c'],
                    'shutdown' => 'MyShutdown',
                    'strip_path_regex' => '/abc/',
                    'project_root_regex' => '/xyz/',
                    'guzzle' => 'MyGuzzle',
                    'memory_limit_increase' => 1234,
                    'discard_classes' => ['SomeClass', 'AnotherClass'],
                    'redacted_keys' => ['one', 'two'],
                    'feature_flags' => [
                        ['name' => 'flag1'],
                        ['name' => 'flag2', 'variant' => 'var1'],
                    ],
                    'max_breadcrumbs' => 100,
                ],
                $this->buildExpectedConfiguration($fullConfig),
            ],
        ];
    }

    public function testFeatureFlagsRequireAName()
    {
        if (method_exists(TestCase::class, 'expectException')) {
            $this->expectException(InvalidConfigurationException::class);
            $this->expectExceptionMessage('Unrecognized option "not name" under "bugsnag.feature_flags.1"');
        } else {
            $this->setExpectedException(
                InvalidConfigurationException::class,
                'Unrecognized option "not name" under "bugsnag.feature_flags.1"'
            );
        }

        $this->processConfiguration([
            'feature_flags' => [
                ['name' => 'flag1'],
                ['not name' => 'flag2'],
            ],
        ]);
    }

    public function testFeatureFlagNameMustBeAString()
    {
        if (method_exists(TestCase::class, 'expectException')) {
            $this->expectException(InvalidConfigurationException::class);
            $this->expectExceptionMessage('Invalid configuration for path "bugsnag.feature_flags.2.name": Feature flag name should be a string, got 3');
        } else {
            $this->setExpectedException(
                InvalidConfigurationException::class,
                'Invalid configuration for path "bugsnag.feature_flags.2.name": Feature flag name should be a string, got 3'
            );
        }

        $this->processConfiguration([
            'feature_flags' => [
                ['name' => 'flag1'],
                ['name' => 'flag2'],
                ['name' => 3],
            ],
        ]);
    }

    private function processConfiguration(array $input)
    {
        $configuration = new Configuration();
        $node = $configuration->getConfigTreeBuilder()->buildTree();

        return $node->finalize($node->normalize($input));
    }

    private function buildExpectedConfiguration(array $overrides = [])
    {
        if ($overrides === []) {
            return $this->defaultConfiguration;
        }

        // find values that have not changed from the default configuration
        $defaults = array_diff_key($this->defaultConfiguration, $overrides);

        // append the defaults to the overrides - there are no shared keys
        // because $defaults only contains keys not present in $overrides
        return array_merge($overrides, $defaults);
    }
}
