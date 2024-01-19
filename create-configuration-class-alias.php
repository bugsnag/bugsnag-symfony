<?php

$class = PHP_MAJOR_VERSION >= 7
    ? \Bugsnag\BugsnagBundle\DependencyInjection\ConfigurationWithReturnType::class
    : \Bugsnag\BugsnagBundle\DependencyInjection\ConfigurationWithoutReturnType::class;

class_alias($class, \Bugsnag\BugsnagBundle\DependencyInjection\Configuration::class);
