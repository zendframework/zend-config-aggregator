<?php
namespace Zend\Expressive\ConfigManager;

use ArrayObject;
use RuntimeException;
use Zend\Stdlib\ArrayUtils;

class ConfigManager
{
    /**
     * @var ArrayObject
     */
    private $config;

    private function loadConfigFromProviders(array $providers)
    {
        $config = [];
        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                throw new RuntimeException("Cannot read config from $providerClass - class cannot be loaded.");
            }
            $provider = new $providerClass();
            if (!is_callable($provider)) {
                throw new RuntimeException(
                    "Cannot read config from $providerClass - config provider must be callable."
                );
            }

            $provided = $provider();
            if (!is_array($provided)) {
                throw new RuntimeException(
                    "Cannot read config from $providerClass - __invoke() does not return array."
                );
            }

            $config = ArrayUtils::merge($config, $provided);
        }
        return $config;
    }

    private function loadConfigFromFiles(array $configFiles)
    {
        $config = [];
        // Load configuration from autoload path
        foreach ($configFiles as $file) {
            $config = ArrayUtils::merge($config, include $file);
        }
        return $config;
    }

    public function __construct(
        array $configFiles,
        array $providers = [],
        $cachedConfigFile = 'data/cache/app_config.php'
    ) {
        if (is_file($cachedConfigFile)) {
            // Try to load the cached config
            $this->config = include $cachedConfigFile;
            return;
        }

        $config = ArrayUtils::merge(
            $this->loadConfigFromProviders($providers),
            $this->loadConfigFromFiles($configFiles)
        );

        // Cache config if enabled
        if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
            file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ";\n");
        }

        // Return an ArrayObject so we can inject the config as a service in Aura.Di
        // and still use array checks like ``is_array``.
        $this->config = new ArrayObject($config, ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return ArrayObject
     */
    public function getMergedConfig()
    {
        return $this->config;
    }
}
