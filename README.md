# php-health-check-ez-pack
A pack of health checks to use with Ibexa platform 3.3 and https://github.com/Financial-Times/php-health-check

## Contents
This health check pack contains a set of checks for eZ Core functionality. Currently this includes checks for search, database and persistence cache.

## Installation
Please refer to the [php health check bundle](https://github.com/Financial-Times/php-health-check) installation guide before installing this.

To install register the health bundle:
```php
<?php
return [
    ...
    FT\HealthCheckBundle\HealthCheckBundle::class => ['all' => true],
    FT\EzHealthCheckBundle\EzHealthCheckBundle::class => ['all' => true]
    ...
]
```

## Config
This bundle currently has 3 configurable health checks:
 * `health_check.ez.search` (For checking search)
 * `health_check.ez.cache` (For checking persistence cache)
 * `health_check.ez.database` (For checking make)

See  the [php health check bundle](https://github.com/Financial-Times/php-health-check) for more information on how to configure them.
