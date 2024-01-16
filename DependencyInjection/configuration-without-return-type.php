<?php

/**
 * This file declares a Configuration class without a return type on the
 * 'getConfigTreeBuilder' for compatibility with PHP 5 where return types are
 * not supported
 *
 * Symfony 7 requires a return type on this method, which is implemented in
 * 'configuration-with-return-type.php' and required in 'Configuration.php' when
 * running on PHP 7+
 */

namespace Bugsnag\BugsnagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration extends BaseConfiguration implements ConfigurationInterface
{
}
