<?php

// protect against this file being required multiple times, leading to duplicate
// class declaration warnings
if (!class_exists(\Bugsnag\BugsnagBundle\DependencyInjection\Configuration::class, false)) {
    $class = PHP_MAJOR_VERSION >= 7
        ? \Bugsnag\BugsnagBundle\DependencyInjection\ConfigurationWithReturnType::class
        : \Bugsnag\BugsnagBundle\DependencyInjection\ConfigurationWithoutReturnType::class;

    class_alias($class, \Bugsnag\BugsnagBundle\DependencyInjection\Configuration::class);
}
