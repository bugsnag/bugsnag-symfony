<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
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
                ->scalarNode('resolver')
                    ->defaultValue(SymfonyResolver::class)
                ->end()
                ->scalarNode('factory')
                    ->defaultValue(ClientFactory::class)
                ->end()
                ->scalarNode('client')
                    ->defaultValue(Client::class)
                ->end()
                ->scalarNode('listener')
                    ->defaultValue(BugsnagListener::class)
                ->end()
                ->arrayNode('notify_release_stages')
                    ->prototype('scalar')->end()
                    ->defaultNull()
                ->end()
                ->arrayNode('filters')
                    ->prototype('scalar')->end()
                    ->defaultNull()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
