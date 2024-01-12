<?php

namespace Bugsnag\BugsnagBundle\DependencyInjection;

if (PHP_MAJOR_VERSION >= 7) {
    require_once __DIR__.'/configuration-with-return-type.php';
} else {
    require_once __DIR__.'/configuration-without-return-type.php';
}
