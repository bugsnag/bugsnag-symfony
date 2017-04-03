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
                    ->defaultValue(getenv('BUGSNAG_API_KEY') ?: null)
                ->end()
                ->scalarNode('endpoint')
                    ->defaultValue(getenv('BUGSNAG_ENDPOINT') ?: null)
                ->end()
                ->booleanNode('callbacks')
                    ->defaultValue(true)
                ->end()
                ->booleanNode('user')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('app_type')
                    ->defaultNull()
                ->end()
                ->scalarNode('app_version')
                    ->defaultNull()
                ->end()
                ->booleanNode('batch_sending')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('hostname')
                    ->defaultNull()
                ->end()
                ->booleanNode('send_code')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('strip_path')
                    ->defaultNull()
                ->end()
                ->scalarNode('project_root')
                    ->defaultNull()
                ->end()
                ->booleanNode('auto_notify')
                    ->defaultValue(true)
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
                    ->treatNullLike([])
                    ->defaultValue([])
                ->end()
                ->arrayNode('notify_excepts')
                    ->prototype('scalar')->end()
                    ->treatNullLike([])
                    ->defaultValue([])
                    ->end()
                ->arrayNode('filters')
                    ->prototype('scalar')->end()
                    ->treatNullLike([])
                    ->defaultValue([])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
