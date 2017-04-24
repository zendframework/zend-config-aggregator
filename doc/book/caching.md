# Caching

Merging configuration on every request is not performant, particularly when
using many configuration files. As such, zend-config-aggregator also
provides the ability to enable a filesystem-based configuration cache.

To enable the configuration cache, pass a cache file name as the second
parameter to the `ConfigAggregator` constructor:

```php
use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

$aggregator = new ConfigAggregator(
    [
        new ArrayProvider([ConfigAggregator::ENABLE_CACHE => true]),
        new PhpFileProvider('*.global.php'),
    ],
    'data/config-cache.php'
);
```

When a cache file is specified, you will also need to add the
`config_cache_enabled` key (which you can also specify via the
`ConfigAggregator::ENABLE_CACHE` constant) somewhere within one of your
configuration providers, and set it to boolean `true`. Using this approach, if
you were to use the globbing pattern `{{,*.}global,{,*.}local}.php` (or similar)
with the `PhpFileProvider`, you could drop a file named `enable-cache.local.php`
into your production deployment with the following contents in order to enable
configuration caching in production:

```php
<?php
use Zend\ConfigAggregator\ConfigAggregator;

return [
    ConfigAggregator::ENABLE_CACHE => true,
];
```

When caching is enabled, the `ConfigAggregator` does not iterate config
providers. Because of that it is very fast, but after it is enabled, you cannot
make any changes to configuration without clearing the cache. **Caching should
be used only in a production environment**, and your deployment process should
clear the cache.
