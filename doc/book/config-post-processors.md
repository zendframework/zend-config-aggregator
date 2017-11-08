# Config post processors

The `ConfigAggregator` can apply post processors to the merged configuration
by aggregating "config processors" passed to its
constructor.  Each processor should be a callable, which consumes the merged configuration
as parameter and returning a configuration array.

```php
$providers = [
    function () {
        return ['foo' => 'bar'];
    },
    new PhpFileProvider('*.global.php'),
];

$processors = [
    function (array $config) {
        return $config + ['post-processed' => true];
    },
];

$aggregator = new ConfigAggregator($providers, null, $processors);
var_dump($aggregator->getMergedConfig());
```

If the processor is a class name, the aggregator automatically instantiates it
before invoking it; as such, any class name you use as a config provider _must_
also define `__invoke()`, and that method _must_ return an array and _may_ consume
the merged configuration as a parameter.

This can be used to mimic the Symfony ParameterBag system: you can specify a config
processor which consumes the merged configuration and resolves the parameters which
might be used in your configuration.

Output from the example:

```php
array(2) {
  'foo' =>
  string(3) "bar"
  'post-processed' =>
  bool(true)
}
```

## Available config post processors

### SymfonyParameterPostProcessor

Resolves parameters within your configuration.

```php
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\SymfonyParameterPostProcessor;

$provider = [
    function () {
        return [
            'session' => [
                'cookie_domain' => '%cookie_domain%',
            ],
            'tracking' => [
                'cookie_domain' => '%cookie_domain%',
            ],
            // Will be converted to %foo% after resolving
            'config_parameter_with_percent' => '%%foo%%',
        ];
    },
];

$parameters = [
    'cookie_domain' => 'example.com',
];

$aggregator = new ConfigAggregator($provider, null, [
    new SymfonyParameterPostProcessor($parameters),
]);
```
