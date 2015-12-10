<?php
namespace Zend\Expressive\ConfigManager;

use Generator;
use Zend\Stdlib\ArrayUtils;

class ConfigManager
{
    /**
     * @var array
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

    private function mergeConfig(&$mergedConfig, $provider, $config)
    {
        if (!is_array($config)) {
            throw new InvalidConfigProviderException(
                sprintf("Cannot read config from %s - it does not return array.", get_class($provider))
            );
        }

        $mergedConfig = ArrayUtils::merge($mergedConfig, $config);
    }

    private function loadConfigFromProviders(array $providers)
    {
        $mergedConfig = [];
        foreach ($providers as $provider) {
            $provider = $this->resolveProvider($provider);
            $config = $provider();
            if ($config instanceof Generator) {
                foreach ($config as $cfg) {
                    $this->mergeConfig($mergedConfig, $provider, $cfg);
                }
            } else {
                $this->mergeConfig($mergedConfig, $provider, $config);
            }
        }
        return $mergedConfig;
    }

    public function __construct(
        array $providers = [],
        $cachedConfigFile = 'data/cache/app_config.php'
    ) {
        if (is_file($cachedConfigFile)) {
            // Try to load the cached config
            $this->config = include $cachedConfigFile;
            return;
        }

        $config = $this->loadConfigFromProviders($providers);

        // Cache config if enabled
        if (isset($config['config_cache_enabled']) && $config['config_cache_enabled'] === true) {
            file_put_contents($cachedConfigFile, '<?php return ' . var_export($config, true) . ";\n");
        }

        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getMergedConfig()
    {
        return $this->config;
    }
}
