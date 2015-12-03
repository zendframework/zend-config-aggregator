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

    private function resolveProvider($provider)
    {
        if (is_string($provider)) {
            if (!class_exists($provider)) {
                throw new InvalidConfigProviderException("Cannot read config from $provider - class cannot be loaded.");
            }
            $provider = new $provider();
        }

        if (!is_callable($provider)) {
            throw new InvalidConfigProviderException(
                sprintf("Cannot read config from %s - config provider must be callable.", get_class($provider))
            );
        }

        return $provider;
    }

    private function loadConfigFromProviders(array $providers)
    {
        $mergedConfig = [];
        foreach ($providers as $provider) {
            $provider = $this->resolveProvider($provider);

            $config = $provider();
            if (!is_array($config)) {
                throw new InvalidConfigProviderException(
                    sprintf("Cannot read config from %s - it does not return array.", get_class($provider))
                );
            }

            $mergedConfig = ArrayUtils::merge($mergedConfig, $config);
        }
        return $mergedConfig;
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
            $this->config = new ArrayObject(include $cachedConfigFile);
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
