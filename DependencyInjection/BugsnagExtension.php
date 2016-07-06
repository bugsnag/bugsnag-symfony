<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BugsnagExtension extends Extension
{
    /**
     * The package version.
     *
     * @return string
     */
    const VERSION = '1.0.0';

    /**
     * Loads a specific configuration.
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        foreach ($config as $key => $value) {
            $container->setParameter('bugnsag.'.$key, $value);
        }

        $container->setParameter('bugnsag.version', static::VERSION);
    }
}
