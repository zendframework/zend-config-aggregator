<?php

namespace ZendTest\ConfigAggregator\Resources;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * @author Maximilian Bösing <max.boesing@check24.de>
 */
class ZendModule implements ServiceProviderInterface, ConfigProviderInterface
{

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return [
            '__class__' => __CLASS__,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'MyInvokable' => InvokableFactory::class,
            ],
        ];
    }
}
