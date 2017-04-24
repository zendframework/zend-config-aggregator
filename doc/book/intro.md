# Introduction

`zend-config-aggregator` is a lightweight library for managing application's configuration. I was designed to be flexible on dev environments and fast on production.

It supports loading and merging configuration from multiple sources: PHP files, arrays, INI/YAML/XML files (using [zend-config](https://zendframework.github.io/zend-config/))

## Basic usage

Standalone `ConfigAggregator` can be used to merge PHP-based configuration files: 

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

Together with `zend-config`, `zend-config-aggregator` can be also used to load configuration in different format - YAML, XML or INI:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;

$aggregator = new ConfigAggregator([
    new ZendConfigProvider('config/*.{json,yaml,php}'),
]);
```

