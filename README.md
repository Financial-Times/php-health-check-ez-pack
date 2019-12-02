# php-health-check-ez-pack
A pack of health checks to use with eZ Platform v1.13 and https://github.com/Financial-Times/php-health-check

## Contents
This health check pack contains a set of checks for eZ Core functionality. Currently this includes checks for search, database and persistence cache.

## Installation
Please refer to the [php health check bundle](https://github.com/Financial-Times/php-health-check) installation guide before installing this.

To install register the health bundle:
```php
    $bundles = [
        ...
        new FT\HealthCheckBundle\HealthCheckBundle(),
        new FT\EzHealthCheckBundle\EzHealthCheckBundle(),
        ...
    ]
```

## Config
This bundle currently has 3 configurable health checks:
 * `health_check.ez.search` (For checking search)
 * `health_check.ez.cache` (For checking persistence cache)
 * `health_check.ez.database` (For checking make)

See  the [php health check bundle](https://github.com/Financial-Times/php-health-check) for more information on how to configure them.
