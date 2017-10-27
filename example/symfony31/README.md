# Running Bugsnag with Symfony 3.1

This example shows how to integrate Bugsnag with Symfony 3.1.  Full instructions on how to set Bugsnag up with Symfony can be found in [the official Bugsnag documentation](https://docs.bugsnag.com/platforms/php/symfony/).


## Installing dependencies

1. Install composer, following the instructions provided in the [composer documentation](http://getcomposer.org/doc/01-basic-usage.md)

2. Install bugsnag using composer

    ```shell
    composer install
    ```

## Configuring Bugsnag

There are two ways of configuring your Bugsnag client.

1. Use environment variables.  In this example you can set the `BUGSNAG_API_KEY` environment variable to your api key.

2. Set the configuration options in the `app/config/config.yml` file:
```yml
bugsnag:
    api_key: YOUR_API_KEY_HERE
```

More information about configuring Bugsnag can be found in [the configuration section of the Bugsnag documentation](https://docs.bugsnag.com/platforms/php/symfony/configuration-options/).

In Symfony the Bugsnag bundle must be registered within the `app/AppKernel.php` file in the `$bundles` array:
```php
$bundles = [
    // ...
    new Bugsnag\BugsnagBundle\BugsnagBundle(),
    // ...
];
```

Bugsnag will now be set up and ready to notify of any exceptions.

## Using callbacks

This example contains an addition in the form of a registered callback.  This callback is present in the `app/AppKernel.php` file, within an overwritten boot function, and attaches some metadata to the passed `report` object from the main library.  It is advised that additional middleware or callbacks should be added to the Bugsnag client in this way.

## Running the example

To run the example:

```shell
composer run
```
