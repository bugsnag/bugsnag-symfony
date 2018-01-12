# Running Bugsnag with Symfony 4

This example shows how to integrate Bugsnag with Symfony 4.  Full instructions on how to set Bugsnag up with Symfony can be found in [the official Bugsnag documentation](https://docs.bugsnag.com/platforms/php/symfony/).


## Installing dependencies

1. Install composer, following the instructions provided in the [composer documentation](http://getcomposer.org/doc/01-basic-usage.md)

2. Install bugsnag using composer

    ```shell
    composer install
    ```

## Configuring Bugsnag

There are two ways of configuring your Bugsnag client.

1. Use environment variables.  In this example you can set the `BUGSNAG_API_KEY` environment variable to your api key.

2. All other configuration should be in the applications `.env` file:

```
BUGSNAG_API_KEY=YOUR_API_KEY_HERE
```

More information about configuring Bugsnag can be found in [the configuration section of the Bugsnag documentation](https://docs.bugsnag.com/platforms/php/symfony/configuration-options/).

In Symfony 4 the Bugsnag bundle should be automatically registered in the `config/bundles.php` file:
```php
return [
    // ...
    Bugsnag\BugsnagBundle\BugsnagBundle::class => ['all' => true],
];
```

Bugsnag will now be set up and ready to notify of any exceptions.

## Manually Acquiring the Bugsnag Bundle

In order to manually use Bugsnag in any of your controllers you will need to require it via dependency injection.

In your `services.yml` file, you should bind the Bugsnag instance to your service parameters by:
```yaml
services:
    # default configuration for services in *this* file
    _defaults:
        # ...
        bind:
            $bugsnag: '@bugsnag'
```

This will make any services with a `$bugsnag` argument in their constructor be passed the Bugsnag bundle.

You will then need to store the Bugsnag instance in your class for later use:

```php
protected $bugsnag;

public function __construct($bugsnag) {
    $this->bugsnag = $bugsnag;
}
```

Which allows Bugsnag to be used within the class as you would any other property.

## Running the example

To run the example:

```shell
php bin/console server:run
```
