<?php

namespace Bugsnag\BugsnagBundle\Tests\DependencyInjection\Compiler;

use Bugsnag\BugsnagBundle\DependencyInjection\Compiler\CallbackRegisteringPass;
use Bugsnag\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CallbackRegisteringPassTest extends TestCase
{
    public function testTaggedCallbacksAreAddedToFactoryServiceDefinition()
    {
        $containerBuilder = new ContainerBuilder();

        $bugsnag = new Definition(Client::class);
        $containerBuilder->setDefinition(CallbackRegisteringPass::BUGSNAG_SERVICE_NAME, $bugsnag);

        $taggedCallbackOne = new Definition();
        $taggedCallbackOne->addTag(CallbackRegisteringPass::TAG_NAME);
        $containerBuilder->setDefinition('callback_1', $taggedCallbackOne);

        $taggedCallbackTwo = new Definition();
        $taggedCallbackTwo->addTag(CallbackRegisteringPass::TAG_NAME, ['method' => 'customMethod']);
        $containerBuilder->setDefinition('callback_2', $taggedCallbackTwo);

        $pass = new CallbackRegisteringPass();
        $pass->process($containerBuilder);

        $this->assertSame(2, count($bugsnag->getMethodCalls()));
        $this->assertSame('registerCallback', $bugsnag->getMethodCalls()[0][0]);
        $this->assertEquals([new Reference('callback_1'), 'registerCallback'], $bugsnag->getMethodCalls()[0][1][0]);
        $this->assertSame('registerCallback', $bugsnag->getMethodCalls()[1][0]);
        $this->assertEquals([new Reference('callback_2'), 'customMethod'], $bugsnag->getMethodCalls()[1][1][0]);
    }
}
