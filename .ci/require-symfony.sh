#!/usr/bin/env sh

## Script to require a given Symfony version, e.g. "5.0"
##
## The version number is passed to composer as-is, so any syntax supported by
## composer is also supported by this script (e.g. "^5", "5.*" etc...)
##
## This script also supports installing the latest version (including
## pre-releases) by passing "latest" instead of a version number. This is done
## using "lastversion", which will be installed with pip if needed
## See https://github.com/dvershinin/lastversion

set -e

if [ $# -eq 0 ]; then
    printf "Error: No Symfony version given\n\n"
    printf "Usage:\n"
    printf "  $ %s <version>\n\n" "$0"
    printf "Examples:\n"
    printf "  $ %s latest\n" "$0"
    printf "  $ %s 5.0.0\n" "$0"
    printf "  $ %s 4.3\n" "$0"

    exit 64
fi

SYMFONY_VERSION=$1

if [ "$SYMFONY_VERSION" = "latest" ]; then
    if ! [ "$(command -v lastversion)" ]; then
        # Install "lastversion" if it's not already installed
        pip install lastversion==v1.1.5
    fi

    composer require "symfony/config:$(lastversion symfony/config --pre)" --no-update
    composer require "symfony/console:$(lastversion symfony/console --pre)" --no-update
    composer require "symfony/dependency-injection:$(lastversion symfony/dependency-injection --pre)" --no-update
    composer require "symfony/http-foundation:$(lastversion symfony/http-foundation --pre)" --no-update
    composer require "symfony/http-kernel:$(lastversion symfony/http-kernel --pre)" --no-update
    composer require "symfony/security-core:$(lastversion symfony/security-core --pre)" --no-update
else
    # If we're requesting a specific version we can simply install it
    composer require "symfony/config:${SYMFONY_VERSION}" --no-update
    composer require "symfony/console:${SYMFONY_VERSION}" --no-update
    composer require "symfony/dependency-injection:${SYMFONY_VERSION}" --no-update
    composer require "symfony/http-foundation:${SYMFONY_VERSION}" --no-update
    composer require "symfony/http-kernel:${SYMFONY_VERSION}" --no-update
    composer require "symfony/security-core:${SYMFONY_VERSION}" --no-update
fi
