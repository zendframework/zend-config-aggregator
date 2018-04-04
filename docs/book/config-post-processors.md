# Post processors

The `ConfigAggregator` can apply post processors to the merged configuration by
aggregating "config processors" passed to its constructor. Each processor
should be a PHP `callable` which consumes the merged configuration as its sole
argument, and which then returns the processed configuration array.

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

Output from the example:

```php
array(2) {
  'foo' =>
  string(3) "bar"
  'post-processed' =>
  bool(true)
}
```

If the processor is a class name, the aggregator automatically instantiates it
before invoking it; as such, any class name you use as a config provider
**must** also define `__invoke()`, and that method **must** return an array and
**may** consume the merged configuration as a parameter.

Post processors can be used to mimic tools such as the [Symfony configuration
parameter system](https://symfony.com/doc/current/service_container/parameters.html).
As an example, you can specify a config processor which consumes the merged
configuration and resolves templated parameters to other parameters within your
configuration.

## Post processor examples

### Symfony ParameterBag Post Processor

The following example resolves templated parameters to either other parameters
within your configuration, or a static set of substitutions. Templated
parameters have the format `%<config_key>%`; `.` characters indicate an
additional level of nesting. If you want to provide configuration parameters
with `%` in the value, you must escape any occurences of `%` by using another
`%`; as examples, `%%bar` or `%%foo%%`.

In the following example, we define a provider that returns a nested array of
configuration. We then define additional parameters and pass them to a 
Symfony DI `ParameterBag`, which we develop a closure over. This closure checks
for parameters in the passed configuration itself, and then attempts to resolve
all configuration values based on the parameters in the `ParameterBag`.

```php

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

$bag = new \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag($parameters);
$resolver = function (array $config) use ($bag) {
    $parametersFromConfiguration = isset($config['parameters']) ? $config['parameters'] : [];
    $bag->add($parametersFromConfiguration);

    // Resolve parameters which probably base on parameters
    $bag->resolve();
    
    // Replace all parameters within the configuration
    $resolved = $bag->resolveValue($config);
    $resolved['parameters'] = $bag->all();
    
    return $bag->unescapeValue($resolved);
};

$aggregator = new ConfigAggregator($provider, null, [
    $resolver,
]);

var_dump($aggregator->getMergedConfig());
```

The above would result in the following when complete:

```php
array(2) {
  'session' =>
  array(1) {
    'cookie_domain' =>
    string(11) "example.com"
  }
  'tracking' =>
  array(1) {
    'cookie_domain' =>
    string(11) "example.com"
  }
  'config_parameter_with_percent' =>
    string(7) "%foo%"
  }
  'parameters' =>
  array(1) {
    'cookie_domain' =>
    string(11) "example.com"
  }
}
```

There is an extension for this feature available via the package
[zendframework/zend-config-aggregator-parameters](https://docs.zendframework.com/zend-config-aggregator-parameters/):

```bash
$ composer require zendframework/zend-config-aggregator-parameters
```
