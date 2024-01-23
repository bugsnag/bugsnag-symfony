<?php

/**
 * This file declares a Configuration class without a return type on the
 * 'getConfigTreeBuilder' for compatibility with PHP 5 where return types are
 * not supported
 *
 * Symfony 7 requires a return type on this method, which is implemented in
 * 'ConfigurationWithReturnType.php' and is used by
 * 'create-configuration-class-alias.php' when running on PHP 7+
 */

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationWithoutReturnType extends BaseConfiguration implements ConfigurationInterface
{
}
