# Running BugSnag with Symfony 6

This example shows how to integrate BugSnag with Symfony 6. Full instructions on how to set BugSnag up with Symfony can be found in [the official BugSnag documentation](https://docs.bugsnag.com/platforms/php/symfony/).
Symfony 6.4 requires PHP 8.1.0 or higher. Alternatively, you can run this using Docker.

## Using Docker

This example comes with a `Dockerfile` you can use to run the project.
Please first build the image:

```shell
docker build -t bugsnag-symfony6 .
```

Then run the container:

```shell
docker run -it --rm -p 8000:8000 bugsnag-symfony6
```

You can then access the application at [http://localhost:8000](http://localhost:8000).

## Manual installation

1. Install composer, following the instructions provided in the [composer documentation](http://getcomposer.org/doc/01-basic-usage.md)

2. Install dependencies using composer

    ```shell
    composer install
    ```

### Running the example

To run the example:

```shell
symfony server:start
```

Or for the command example:

```shell
php bin/console app:crash
```

## Configuring BugSnag

There are two ways of configuring your BugSnag client.

1. Set the configuration options in `config/packages/bugsnag.yaml`.  These values will automatically be loaded in when the application starts.

```yaml
bugsnag:
    api_key: 'YOUR_API_KEY'
    auto_notify: true
```

2. Use environment variables.  In this example you can set the `BUGSNAG_API_KEY` environment variable to your api key. This can also be set in the applications `.env` file:

```env
BUGSNAG_API_KEY=YOUR_API_KEY_HERE
```

More information about configuring BugSnag can be found in [the configuration section of the BugSnag documentation](https://docs.bugsnag.com/platforms/php/symfony/configuration-options/).

In Symfony 6 the BugSnag bundle should be automatically registered in the `config/bundles.php` file:

```php
return [
    // ...
    Bugsnag\BugsnagBundle\BugsnagBundle::class => ['all' => true],
];
```

BugSnag will now be set up and ready to notify of any exceptions.

## Manually Acquiring the BugSnag Bundle

In order to use BugSnag in any of your classes you will need to require it via dependency injection.

In your services.yaml file, bind the Bugsnag\Client class to the @bugsnag service:

```yaml
services:
    # resolve "Bugsnag\Client" to the BugSnag service
    Bugsnag\Client: '@bugsnag'
```

Any of your classes requiring BugSnag can use the type Bugsnag\Client to access it:

```php
private $bugsnag;

public function __construct(\Bugsnag\Client $bugsnag)
{
    $this->bugsnag = $bugsnag;
}
```

Which allows BugSnag to be used within the class as you would any other property.
