<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BugsnagExtension extends Extension
{
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

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');

        foreach ($config as $key => $value) {
            $container->setParameter('bugsnag.'.$key, $value);
        }

        if ($container->hasParameter('kernel.project_dir')) {
            $symfonyRoot = $container->getParameter('kernel.project_dir');
        } else {
            $symfonyRoot = $container->getParameter('kernel.root_dir').'/../';
        }

        $container->setParameter('bugsnag.symfony_root', $symfonyRoot);
    }
}
