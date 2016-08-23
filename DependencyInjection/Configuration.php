<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Get the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bugsnag');

        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->defaultValue(getenv('BUGSNAG_API_KEY'))
                ->end()
                ->scalarNode('endpoint')
                    ->defaultValue(getenv('BUGSNAG_ENDPOINT'))
                ->end()
                ->scalarNode('callbacks')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('strip_path')
                    ->defaultNull()
                ->end()
                ->scalarNode('project_root')
                    ->defaultNull()
                ->end()
                ->scalarNode('exception_listener')
                    ->defaultValue('Bugsnag\BugsnagBundle\EventListener\ExceptionListener')
                ->end()
                ->arrayNode('notify_release_stages')
                    ->prototype('scalar')->end()
                    ->defaultValue(array())
                ->end()
                ->arrayNode('filters')
                    ->prototype('scalar')->end()
                    ->defaultValue(['password'])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
