<?php

namespace ZendTest\ConfigAggregator;

use PHPUnit\Framework\TestCase;
use Zend\ConfigAggregator\ZendModuleProvider;
use Zend\ServiceManager\Factory\InvokableFactory;
use ZendTest\ConfigAggregator\Resources\ZendModule;
use ZendTest\ConfigAggregator\Resources\ZendModuleWithoutImplementingInterfaces;

/**
 * @author Maximilian Bösing <max.boesing@check24.de>
 */
class ZendModuleProviderTest extends TestCase
{

    public function testCanProvideDependenciesFromInterface()
    {
        $module = new ZendModule();
        $provider = new ZendModuleProvider($module);

        $config = $provider();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertSame([
            'factories' => [
                'MyInvokable' => InvokableFactory::class,
            ]
        ], $config['dependencies']);
    }

    public function testCanProvideAnyConfigValue()
    {
        $module = new ZendModule();
        $provider = new ZendModuleProvider($module);

        $config = $provider();

        $this->assertArrayHasKey('__class__', $config);
        $this->assertSame(ZendModule::class, $config['__class__']);
    }

    public function testCanProvideDependenciesFromModuleWithoutInterface()
    {
        $module = new ZendModuleWithoutImplementingInterfaces();
        $provider = new ZendModuleProvider($module);

        $config = $provider();

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertSame([
            'factories' => [
                'SomeObject' => InvokableFactory::class,
            ]
        ], $config['dependencies']);
    }
}
