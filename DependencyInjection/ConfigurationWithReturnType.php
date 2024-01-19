<?php

/**
 * This file declares a Configuration class with a return type on the
 * 'getConfigTreeBuilder' for compatibility with Symfony 7+
 *
 * This is required to be in a separate file with
 * 'create-configuration-class-alias.php' requiring it at runtime so that we can
 * maintain compatibility with PHP 5 where return types are not supported
 */

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithReturnType extends BaseConfiguration implements ConfigurationInterface
{
    /**
     * Get the configuration tree builder.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        return parent::getConfigTreeBuilder();
    }
}
