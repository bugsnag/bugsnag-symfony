<?php

namespace Bugsnag\BugsnagBundle;

use Bugsnag\BugsnagBundle\DependencyInjection\Compiler\CallbackRegisteringPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BugsnagBundle extends Bundle
{
    /**
     * The package version.
     *
     * @return string
     */
    const VERSION = '1.0.0';

    /**
     * {@inheritdoc}
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CallbackRegisteringPass());
    }
}
