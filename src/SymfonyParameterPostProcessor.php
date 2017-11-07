<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregator;

use Symfony\Component\DependencyInjection\Exception\ParameterCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Handles the parameter conversion within the injected config by using symfony dependency-injection parameter bag.
 */
class SymfonyParameterPostProcessor
{

    /**
     * @var ParameterBagInterface
     */
    private $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = new ParameterBag($parameters);
    }

    /**
     * @param array $config
     *
     * @return array
     * @throws ParameterCircularReferenceException
     * @throws ParameterNotFoundException
     * @throws RuntimeException
     */
    public function __invoke(array $config)
    {
        // Recursivly resolve values
        $config = $this->resolve($config);
        $config['parameters'] = $this->parameters->all();

        return $config;
    }

    /**
     * Resolves the parameters within the configuration. If the configuration provides parameters, we are using them
     * as possible parameters.
     *
     * @param array $config
     *
     * @return array
     */
    private function resolve(array $config)
    {
        $parameters = $this->parameters;
        $configParameters = isset($config['parameters']) ? $config['parameters'] : [];
        $parameters->add($configParameters);

        $parameters->resolve();
        $resolved = $parameters->resolveValue($config);

        return $parameters->unescapeValue($resolved);
    }
}
