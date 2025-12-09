<?php

/**
 * Script to setup the bugsnag.yaml config file according to the currently set
 * environment variables that start with "BUGSNAG_"
 */

const CONFIG_FILE = __DIR__ . '/config/packages/bugsnag.yaml';

$yaml = "bugsnag:\n";

foreach ($_ENV as $key => $value) {
    if (strpos($key, 'BUGSNAG_') !== 0 || $value === null || $value === '') {
        continue;
    }

    $name = strtolower(substr($key, strlen('BUGSNAG_')));

    $yaml .= <<<YAML
      {$name}: {$value}\n
    YAML;
}

file_put_contents(CONFIG_FILE, $yaml);
