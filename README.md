Expressive Configuration Manager
================================

[![Build Status](https://travis-ci.org/mtymek/expressive-config-manager.svg?branch=master)](https://travis-ci.org/mtymek/expressive-config-manager)

Lightweight library for collecting and merging configuration from different sources. 

It is designed for [zend-expressive](https://github.com/zendframework/zend-expressive) 
applications, but it can work with any PHP project.
 
Usage
-----

### Config files

At the basic level, ConfigManager can be used to merge PHP-based configuration files: 

```php
use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\GlobFileProvider;

$configManager = new ConfigManager(
    [
        new GlobFileProvider('*.global.php')
    ]
);

var_dump((array)$configManager->getMergedConfig());
```

Each file should return plain PHP array:

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

### Config providers

ConfigManager works by aggregating "Config Providers". Each provider should be a callable,
returning configuration array (or generator) to be merged.

```php
$configManager = new ConfigManager(
    [
        function () { return ['foo' => 'bar']; },
        new GlobFileProvider('*.global.php'),
    ]
);
var_dump((array)$configManager->getMergedConfig());
```

If provider is a class name, it is automatically instantiated. This can be used to mimic
ZF2 module system - you can specify list of config classes from different packages,
and aggregated configuration will be available to your application. Or, as a library
owner you can distribute config class with default values.

Example:


```php
class ApplicationConfig
{
    public function __invoke()
    {
        return ['foo' => 'bar'];
    }
}

$configManager = new ConfigManager(
    [
        ApplicationConfig::class,
        new GlobFileProvider('*.global.php'),
    ]
);
var_dump((array)$configManager->getMergedConfig());
```

Output from both examples will be the same:

```php
array(4) {
  'foo' =>
  string(3) "bar"
  'db' =>
  array(1) {
    'dsn' =>
    string(9) "mysql:..."
  }
  'cache_storage' =>
  string(5) "redis"
  'redis' =>
  array(0) {
  }
}
```
