<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CallbackRegisteringPass implements CompilerPassInterface
{
    const BUGSNAG_SERVICE_NAME = 'bugsnag';
    const TAG_NAME = 'bugsnag.callback';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::BUGSNAG_SERVICE_NAME)) {
            return;
        }

        // Get the Bugsnag service
        $bugsnag = $container->findDefinition(self::BUGSNAG_SERVICE_NAME);

        // Get all services tagged as a callback
        $callbackServices = $container->findTaggedServiceIds(self::TAG_NAME);

        // Register each callback on the bugsnag service
        foreach ($callbackServices as $id => $tags) {
            foreach ($tags as $attributes) {
                // Get the method name to call on the service from the tag definition,
                // defaulting to registerCallback
                $method = isset($attributes['method']) ? $attributes['method'] : 'registerCallback';
                $bugsnag->addMethodCall('registerCallback', [[new Reference($id), $method]]);
            }
        }
    }
}
