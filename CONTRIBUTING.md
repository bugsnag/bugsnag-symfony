Contributing
============

-   [Fork](https://help.github.com/articles/fork-a-repo) the [notifier on github](https://github.com/bugsnag/bugsnag-symfony)
-   Build and test your changes:
```
composer install && ./vendor/bin/phpunit
```

-   Commit and push until you are happy with your contribution
-   [Make a pull request](https://help.github.com/articles/using-pull-requests)
-   Thanks!

Testing
=======

Unit tests
----------

To run the unit tests, install the dependencies with Composer:

```sh
$ composer install
```

Then run the Composer "test" script:

```sh
$ composer test
```

End-to-end tests
----------------

These tests are implemented with our notifier testing tool [Maze Runner](https://github.com/bugsnag/maze-runner). This requires a recent version of Ruby to run

End to end tests are written in cucumber-style `.feature` files, and need Ruby-backed "steps" in order to know what to run. The tests are located in the top level [`features`](/features/) directory

Install Maze Runner using Bundler:

```sh
$ bundle install
```

Configure the tests with the following environment variables:

- `PHP_VERSION` — the PHP version to run the tests with, e.g. "8.0". This must match one of the [published PHP Docker image tags](https://hub.docker.com/_/php)
- `SYMFONY_VERSION` — the version of Symfony to run the tests against, e.g. "5". There must be a matching fixture for the specified version in the [`features/fixtures`](/features/fixtures/) directory

Run Maze Runner with Bundler:

```sh
$ PHP_VERSION=8.0 SYMFONY_VERSION=5 bundle exec maze-runner
```

To enable additional output for debugging, set the `DEBUG` environment variable:

```sh
$ DEBUG=1 PHP_VERSION=8.0 SYMFONY_VERSION=5 bundle exec maze-runner
```

Releasing
=========

1. Commit all outstanding changes
2. Bump the version in `BugsnagBundle.php`
3. Update the CHANGELOG.md, and README if appropriate.
4. Commit, tag push
    ```
    git commit -am v1.x.x
    git tag v1.x.x
    git push origin master v1.x.x
    ```
5. Update the setup guide for Symfony on docs.bugsnag.com with any new content
