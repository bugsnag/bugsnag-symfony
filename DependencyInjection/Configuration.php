<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Bugsnag\BugsnagBundle\EventListener\BugsnagListener;
use Bugsnag\BugsnagBundle\EventListener\BugsnagShutdown;
use Bugsnag\BugsnagBundle\Request\SymfonyResolver;
use Bugsnag\Client;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * The name of the root of the configuration.
     *
     * @var string
     */
    const ROOT_NAME = 'bugsnag';

    /**
     * Get the configuration tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NAME);
        $rootNode = $this->getRootNode($treeBuilder);

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
                ->scalarNode('release_stage')
                    ->defaultNull()
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
                ->arrayNode('filters')
                    ->prototype('scalar')->end()
                    ->treatNullLike([])
                    ->defaultValue([])
                ->end()
                ->scalarNode('shutdown')
                    ->defaultValue(BugsnagShutdown::class)
                ->end()
                ->scalarNode('strip_path_regex')
                    ->defaultNull()
                ->end()
                ->scalarNode('project_root_regex')
                    ->defaultNull()
                ->end()
                ->scalarNode('guzzle')
                    ->defaultNull()
                ->end()
                ->scalarNode('memory_limit_increase')
                    ->defaultFalse()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * Returns the root node of TreeBuilder with backwards compatibility
     * for pre-Symfony 4.1.
     *
     * @param TreeBuilder $treeBuilder a TreeBuilder to extract/create the root node
     *                                 from
     *
     * @return NodeDefinition the root node of the config
     */
    protected function getRootNode(TreeBuilder $treeBuilder)
    {
        if (\method_exists($treeBuilder, 'getRootNode')) {
            return $treeBuilder->getRootNode();
        }

        return $treeBuilder->root(self::ROOT_NAME);
    }
}
