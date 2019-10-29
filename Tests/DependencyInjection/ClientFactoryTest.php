<?php

namespace Bugsnag\BugsnagBundle\Tests\DependencyInjection;

use Bugsnag\BugsnagBundle\DependencyInjection\ClientFactory;
use Bugsnag\BugsnagBundle\EventListener\BugsnagShutdown;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Shutdown\ShutdownStrategyInterface;
use GrahamCampbell\TestBenchCore\MockeryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientFactoryTest extends TestCase
{
    use MockeryTrait;

    protected $factoryArgs;

    public function setUp()
    {
        parent::setUp();
        $this->setDefaultArgs();
    }

    public function testShutdownStrategyIsPassedToClient()
    {
        $shutdown = \Mockery::mock(BugsnagShutdown::class);
        $shutdown->shouldReceive('registerShutdownStrategy')->once();
        $this->factoryArgs['shutdownStrategy'] = $shutdown;
        $factory = self::createFactory($this->factoryArgs);
        $client = $factory->make();

        $shutdown->shouldHaveReceived('registerShutdownStrategy', [$client]);
        $this->tearDownMockery();
    }


    /**
     * Creates a factory from arguments supplied as a hash
     * @param $args
     * @return ClientFactory
     * @throws \ReflectionException
     */
    protected static function createFactory($args)
    {
        $class = new \ReflectionClass(ClientFactory::class);
        return $class->newInstanceArgs(array_values($args));
    }

    /**
     * Sets a hash of default variables that can be accessed/edited to configure different factories
     */
    protected function setDefaultArgs()
    {
        $this->factoryArgs = [
            'resolver' => \Mockery::mock(SymfonyResolver::class),
            'tokens' => null,
            'checker' => null,
            'key' => null,
            'endpoint' => null,
            'callbacks' => true,
            'user' => true,
            'type' => null,
            'version' => true,
            'batch' => null,
            'hostname' => null,
            'code' => true,
            'strip' => null,
            'project' => null,
            'root' => null,
            'env' => null,
            'stage' => null,
            'stages' => null,
            'filters' => null,
            'shutdownStrategy' => null,
        ];
    }
}
