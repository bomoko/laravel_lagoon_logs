# Lagoon Logs for Laravel
Monolog channel and formatter for Laravel logging into Lagoon.

## Basic Usage

Installing this package makes the channel `lagoon` available in your logging config.

You can install the package by simply running `composer require amazeeio/lagoon-logs` - the package should be installed and autodiscovered by Laravel.

It's important to note that this is essentially a wrapper around a Monolog Logger with a specifically set UDP SocketHandler and LogstashFormatter - therefore, it really only makes sense to use this _when_ deployed to a Lagoon instance.

In a vanilla Laravel installation, this is most easily done by setting the environment variable `LOG_CHANNEL` to `lagoon`.

## Advanced Usage

This package provides a default configuration via `./config/logging.php` that will be merged with your application's
`./config/logging.php`. If you need more control over your logging stack, you can simply override the default config by
adding `lagoon` channel in your `./config/logging.php` file.

For instance, if you need to configure a custom formatter, you can add:
```
'lagoon' => [
    'tap' => [App\Logging\CustomizeFormatter::class],
],
```

See [the Laravel docs](https://laravel.com/docs/11.x/logging) for more on customizing logging.

Additionally, the following environmental variables are available:

```
LOG_LEVEL=debug
LAGOON_LOGS_HOST="application-logs.lagoon.svc"
LAGOON_LOGS_PORT=5140
LAGOON_LOGS_IDENTIFIER="my-application"
```

Note, if `LAGOON_LOGS_IDENTIFIER` value is not provided then this package will attempt to create it by looking up Lagoon
environment details using a combination `LAGOON_PROJECT` and `LAGOON_GIT_SAFE_BRANCH`.
