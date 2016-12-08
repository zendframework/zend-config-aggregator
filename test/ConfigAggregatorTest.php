<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ConfigAggregator;

use org\bovigo\vfs\vfsStream;
use PHPUnit_Framework_TestCase as TestCase;
use StdClass;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\InvalidConfigProviderException;
use ZendTest\ConfigAggregator\Resources\BarConfigProvider;
use ZendTest\ConfigAggregator\Resources\FooConfigProvider;

class ConfigAggregatorTest extends TestCase
{
    public function testConfigAggregatorRisesExceptionIfProviderClassDoesNotExist()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigAggregator(['NonExistentConfigProvider']);
    }

    public function testConfigAggregatorRisesExceptionIfProviderIsNotCallable()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigAggregator([StdClass::class]);
    }

    public function testConfigAggregatorMergesConfigFromProviders()
    {
        $aggregator = new ConfigAggregator([FooConfigProvider::class, BarConfigProvider::class]);
        $config = $aggregator->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bat'], $config);
    }

    public function testProviderCanBeClosure()
    {
        $aggregator = new ConfigAggregator([
            function () {
                return ['foo' => 'bar'];
            },
        ]);
        $config = $aggregator->getMergedConfig();
        $this->assertEquals(['foo' => 'bar'], $config);
    }

    public function testProviderCanBeGenerator()
    {
        $aggregator = new ConfigAggregator([
            function () {
                yield ['foo' => 'bar'];
                yield ['baz' => 'bat'];
            },
        ]);
        $config = $aggregator->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'baz' => 'bat'], $config);
    }

    public function testConfigAggregatorCanCacheConfig()
    {
        vfsStream::setup(__FUNCTION__);
        $cacheFile = vfsStream::url(__FUNCTION__) . '/expressive_config_loader';
        new ConfigAggregator([
            function () {
                return ['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true];
            }
        ], $cacheFile);
        $this->assertTrue(file_exists($cacheFile));
        $cachedConfig = include $cacheFile;
        $this->assertInternalType('array', $cachedConfig);
        $this->assertEquals(['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true], $cachedConfig);
    }

    public function testConfigAggregatorCanLoadConfigFromCache()
    {
        $expected = [
            'foo' => 'bar',
            ConfigAggregator::ENABLE_CACHE => true,
        ];

        $root = vfsStream::setup(__FUNCTION__);
        vfsStream::newFile('expressive_config_loader')
            ->at($root)
            ->setContent('<' . '?php return ' . var_export($expected, true) . ';');
        $cacheFile = vfsStream::url(__FUNCTION__ . '/expressive_config_loader');

        $aggregator = new ConfigAggregator([], $cacheFile);
        $mergedConfig = $aggregator->getMergedConfig();

        $this->assertInternalType('array', $mergedConfig);
        $this->assertEquals($expected, $mergedConfig);
    }
}
