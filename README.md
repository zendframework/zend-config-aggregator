Expressive Configuration Manager
================================

[![Build Status](https://travis-ci.org/mtymek/expressive-config-manager.svg?branch=master)](https://travis-ci.org/mtymek/expressive-config-manager)

Lightweight library for collecting and merging configuration from different sources. 

It is designed for [zend-expressive](https://github.com/zendframework/zend-expressive) 
applications, but it can work with any PHP project.
 
Usage
-----

### Config files

Each config file should return plain PHP array:

```php
// config1.php
return [
    'key1' => 'foo',
    'key2' => 'bar',
];

// config2.php
return [
    'key1' => 'baz',
    'key3' => 'foobar',
];
```

```php
use Zend\Expressive\ConfigManager\ConfigManager;

$configManager = new ConfigManager(
    ['config1.php', 'config2.php']
);
var_dump((array)$configManager->getMergedConfig());
```

`ConfigManager` will combine arrays from all files together, so code above will 
generate following output:

```
// output:
array(3) {
  'key1' =>
  string(3) "baz"
  'key2' =>
  string(3) "bar"
  'key3' =>
  string(6) "foobar"
}
```

You can use it together with `glob` to read all files from certain directory:

```php
use Zend\Stdlib\Glob;

// loads all *.global.php and *.local.php files from config/ directory  
$configManager = new ConfigManager(
    Glob::glob('config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE)
);
```

### Config providers

On top of merging files, `ConfigManager` can also add configuration read from
objects implementing `ConfigProviderInterface`:

```php
class AppConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig()
    {
        return ['foo' => 'bar'];
    }
}
```

You can pass list of config providers as a second parameter when constructing object:

```php  
$configManager = new ConfigManager(
    Glob::glob('config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE),
    [AppConfigProvider::class, BlogConfigProvider::class]
);
```

This functionality allows behavior similar to ZF2 modules - your library can be 
shipped with configuration class that provides default values. 
