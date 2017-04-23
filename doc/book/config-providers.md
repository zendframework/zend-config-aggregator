# Config providers

`ConfigAggregator` works by aggregating "Config Providers" passed to its
constructor.  Each provider should be a callable, returning a configuration
array (or a PHP generator) to be merged.

```php
$aggregator = new ConfigAggregator([
    function () {
        return ['foo' => 'bar'];
    },
    new PhpFileProvider('*.global.php'),
]);
var_dump($aggregator->getMergedConfig());
```

If the provider is a class name, the aggregator automatically instantiates it.
This can be used to mimic the Zend Framework module system: you can specify a
list of config providers from different packages, and aggregated configuration
will be available to your application.

As a library owner, you can distribute your own configuration providers that
provide default values for use with your library.

As an example:

```php
class ApplicationConfig
{
    public function __invoke()
    {
        return ['foo' => 'bar'];
    }
}

$aggregator = new ConfigAggregator(
    [
        ApplicationConfig::class,
        new PhpFileProvider('*.global.php'),
    ]
);
var_dump($aggregator->getMergedConfig());
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

### Generators

Config providers can be written as generators. This way single callable can provide 
multiple configurations:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\Stdlib\Glob;

$aggregator = new ConfigAggregator([
    function () { 
        foreach (Glob::glob('data/*.global.php', Glob::GLOB_BRACE) as $file) {
            yield include $file;
        } 
    }        
]
);
var_dump($aggregator->getMergedConfig());
```

The providers `PhpFileProvider` is implemented using generators.


## Available config providers

### PhpFileProvider
 
Loads configuration from PHP files returning arrays, such as this one:

```php
return [
    'db' => [
        'dsn' => 'mysql:...',
    ],    
];
```

Wildcards are supported:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

$aggregator = new ConfigAggregator(
    [
        new PhpFileProvider('config/*.global.php'),        
    ]
);
```

The example above will merge all matching files from the `config/` directory. If
you have files such as `app.global.php` or `database.global.php` in that
directory, they will be loaded using this above lines of code.

Globbing defaults to PHP's `glob()` function. However, if `Zend\Stdlib\Glob` is
available, it will use that to allow for cross-platform glob patterns, including
brace notation: `'config/autoload/{{,*.}global,{,*.}local}.php'`. Install
zendframework/zend-stdlib to utilize this feature.
    
### ZendConfigProvider

Sometimes using plain PHP files may be not enough; you may want to build your configuration 
from multiple files of different formats, such as INI, YAML, or XML.
zend-config-aggregator allows you to do so via its `ZendConfigProvider`:

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\ZendConfigProvider;

$aggregator = new ConfigAggregator(
    [
        new ZendConfigProvider('*.global.json'),
        new ZendConfigProvider('database.local.ini'),
    ]
);
```

These could even be combined into a single glob statement:

```php
$aggregator = new ConfigAggregator(
    [
        new ZendConfigProvider('*.global.json,database.local.ini'),
    ]
);
```

`ZendConfigProvider` accepts wildcards and autodetects the config type based on
file extension. 

ZendConfigProvider requires two packages to be installed:
`zendframework/zend-config` and `zendframework/zend-servicemanager`. Some config
readers (JSON, YAML) may need additional dependencies; please refer to
[the zend-config manual](https://docs.zendframework.com/zend-config/reader/)
for more details.
