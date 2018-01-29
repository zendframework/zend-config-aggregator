# Introduction

`zend-config-aggregator` is a lightweight library for managing application
configuration. It was designed to be flexible in dev environments and fast in
production.

It supports loading and merging configuration from multiple sources: PHP files,
arrays, or INI/YAML/XML files (using [zend-config](https://docs.zendframework.com/zend-config/))

It also provides the ability to post process the merged configuration to apply e.g. parameter
handling like [symfony/dependency-injection](https://symfony.com/doc/current/service_container/parameters.html#parameters-in-configuration-files)

## Basic usage

The standalone `ConfigAggregator` can be used to merge PHP-based configuration files:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

$aggregator = new ConfigAggregator([
    new PhpFileProvider('*.global.php'),
]);

var_dump($aggregator->getMergedConfig());
```

Using this provider, each file should return a PHP array:

```php
// db.global.php
return [
    'db' => [
        'dsn' => 'mysql:...',
    ],
];

// cache.global.php
return [
    'cache_storage' => 'redis',
    'redis' => [ ... ],
];
```

Result:

```php
array(3) {
  'db' =>
  array(1) {
    'dsn' =>
    string(9) "mysql:..."
  }
  'cache_storage' =>
  string(5) "redis"
  'redis' =>
  array(0) {
     ...
  }
}
```

Configuration is merged in the same order as it is passed, with later entries having precedence.

Together with `zend-config`, `zend-config-aggregator` can be also used to load
configuration in different formats, including YAML, JSON, XML, or INI:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;

$aggregator = new ConfigAggregator([
    new ZendConfigProvider('config/*.{json,yaml,php}'),
]);
```

You can also supply [post processors](config-post-processors.md) for
configuration. These are PHP callables that accept the merged configuration as
an argument, do something with it, and return configuration on completion. This
could be used, for example, to allow templating parameters that are used in
multiple locations and resolving them to a single value later.
