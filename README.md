# zend-config-aggregator

[![Build Status](https://travis-ci.org/zendframework/zend-config-aggregator.svg?branch=master)](https://travis-ci.org/zendframework/zend-config-aggregator) 

Aggregates and merges configuration, from a variety of formats. Supports caching
for fast bootstrap in production environments.
 
## Usage

The standalone `ConfigAggregator` can be used to merge PHP-based configuration
files: 

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

Configuration is merged in the same order as it is passed, with later entries
having precedence.

Together with `zend-config`, `zend-config-aggregator` can be also used to load
configuration in different formats, including YAML, JSON, XML, or INI:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;

$aggregator = new ConfigAggregator([
    new ZendConfigProvider('config/*.{json,yaml,php}'),
]);
```

For more details, please refer to the [documentation](https://docs.zendframework.com/zend-config-aggregator/).

-----

- File issues at https://github.com/zendframework/zend-config-aggregator/issues
- Documentation is at https://docs.zendframework.com/zend-config-aggregator/
