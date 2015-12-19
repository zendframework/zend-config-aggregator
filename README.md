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

var_dump($configManager->getMergedConfig());
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

ConfigManager works by aggregating "Config Providers" passed when creating object. 
Each provider should be a callable, returning configuration array  (or generator) 
to be merged.

```php
$configManager = new ConfigManager(
    [
        function () { return ['foo' => 'bar']; },
        new GlobFileProvider('*.global.php'),
    ]
);
var_dump($configManager->getMergedConfig());
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
var_dump($configManager->getMergedConfig());
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

### Caching

In order to enable config cache, you need to add `config_cache_enabled` key to the config,
and set it to `TRUE`.

By default, cache is stored in `data/cache/app_config.php` file. This can be overridden
using second argument of `ConfigManager`'s constructor:

```php
$configManager = new ConfigManager(
    [
        function () { return ['config_cache_enabled' => true]; },
        new GlobFileProvider('*.global.php'),
    ],
    'data/config-cache.php'
);
```

When caching is enabled, `ConfigManager` does not iterate config providers. Because of that
it is very fast, but after it is enabled you cannot do any changes to configuration without
clearing the cache. **Caching should be used only in production environment**, and your 
deployment process should clear the cache.

### Generators

Config providers can be written as generators. This way single callable can provide 
multiple configurations:

```php
$configManager = new ConfigManager(
    [
        function () { 
            foreach (Glob::glob('data/*.global.php', Glob::GLOB_BRACE) as $file) {
                yield include $file;
            } 
        }        
    ]
);
var_dump($configManager->getMergedConfig());
```

`GlobFileProvider` is implemented using generators.


Available config providers
--------------------------

### GlobFileProvider
 
Loads configuration from PHP files returning arrays, like this one:
```php
return [
    'db' => [
        'dsn' => 'mysql:...',
    ],    
];
```

Wildcards are supported:  

```php
$configManager = new ConfigManager(
    [
        new GlobFileProvider('config/*.global.php'),        
    ]
);
```

Example above will merge all matching files from `config` directory - if you have 
files like `app.global.php`, `database.global.php`, they will be loaded using this few 
lines of code.

Internally, `ZendStdlib\Glob` is used for resolving wildcards, meaning that you can 
use more complex patterns (for instance: `'config/autoload/{{,*.}global,{,*.}local}.php'`), 
that will work even on Windows platform. 
    
### ZendConfigProvider

Sometimes using plain PHP files may be not enough - you may want to build your configuration 
from multiple files of different formats: INI, YAML, or XML. For this purpose you can 
leverage `ZendConfigProvider`:

```php
$configManager = new ConfigManager(
    [
        new ZendConfigProvider('*.global.json'),
        new ZendConfigProvider('database.local.ini')
    ]
);
```

`ZendConfigProvider` accepts wildcards and autodetects config type based on file extension. 

ZendConfigProvider requires two packages to be installed: `zendframework/zend-config` and 
`zendframework/zend-servicemanager`. Some config readers (JSON, YAML) may need additional
dependencies - please refer to [Zend Config Manual](http://framework.zend.com/manual/current/en/index.html#zend-config)
for more details.
