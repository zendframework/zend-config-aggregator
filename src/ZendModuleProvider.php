<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregator;

use InvalidArgumentException;
use Traversable;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

/**
 * Provide a configuration by using zend-modulemanager Module files.
 */
class ZendModuleProvider
{

    /**
     * @var object
     */
    private $module;

    /**
     * @var array
     */
    private $dependencies = [];

    /**
     * @var string
     */
    private $dependenciesIdentifier = 'dependencies';

    /**
     * @param object $module
     */
    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @return array
     */
    public function __invoke()
    {
        return array_replace_recursive($this->getModuleConfig(), [
            $this->getDependenciesIdentifier() => $this->getModuleDependencies(),
            'controllers' => $this->getControllerDependencies(),
        ]);
    }

    /**
     * @return array
     */
    private function getModuleDependencies()
    {
        $module = $this->module;
        if (!$module instanceof ServiceProviderInterface) {
            return $this->dependencies;
        }

        return array_replace_recursive($this->dependencies, $this->convert($module->getServiceConfig()));
    }

    /**
     * @return array
     */
    private function getModuleConfig()
    {
        $module = $this->module;

        if (!$module instanceof ConfigProviderInterface
            && !is_callable([$module, 'getConfig'])
        ) {
            return [];
        }

        $converted = $this->convert($module->getConfig());

        if (isset($converted['service_manager'])) {
            $this->dependencies = $converted['service_manager'] ?: [];
            unset($converted['service_manager']);
        }

        return $converted;
    }

    /**
     * @param array|Traversable $config
     *
     * @return array
     */
    private function convert($config)
    {
        if ($config instanceof Traversable) {
            $config = iterator_to_array($config);
        }

        if (!is_array($config)) {
            throw new InvalidArgumentException(sprintf(
                'Config being merged must be an array, '
                . 'implement the Traversable interface, or be an '
                . 'instance of Zend\Config\Config. %s given.',
                is_object($config) ? get_class($config) : gettype($config)
            ));
        }

        return $config;
    }

    /**
     * @return string
     */
    public function getDependenciesIdentifier()
    {
        return $this->dependenciesIdentifier;
    }

    /**
     * @param string $dependenciesIdentifier
     *
     * @return void
     */
    public function setDependenciesIdentifier($dependenciesIdentifier)
    {
        $this->dependenciesIdentifier = (string) $dependenciesIdentifier;
    }

    private function getControllerDependencies()
    {
        $module = $this->module;
        if (!$module instanceof ControllerProviderInterface) {
            return [];
        }

        return $module->getControllerConfig();
    }
}
