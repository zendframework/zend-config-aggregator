<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ConfigAggregator;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use stdClass;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\InvalidConfigProcessorException;
use Zend\ConfigAggregator\InvalidConfigProviderException;
use ZendTest\ConfigAggregator\Resources\BarConfigProvider;
use ZendTest\ConfigAggregator\Resources\FooConfigProvider;
use ZendTest\ConfigAggregator\Resources\FooPostProcessor;

use function file_exists;
use function var_export;

class ConfigAggregatorTest extends TestCase
{
    private $cacheFile;
    private $defaultFile;
    private $lockFile;

    protected function setUp()
    {
        parent::setUp();
        $dir = sys_get_temp_dir() . '/expressive_config_loader';
        if (! is_dir($dir)) {
            mkdir($dir);
        }
        $this->cacheFile = $dir . '/cache';
        $this->defaultFile = $dir . '/default';
        $this->lockFile = sys_get_temp_dir() . '/' . basename($this->cacheFile) . '.tmp';
    }

    protected function tearDown()
    {
        @unlink($this->cacheFile);
        @unlink($this->defaultFile);
        @unlink($this->lockFile);
        @rmdir(dirname($this->cacheFile));
    }

    public function testConfigAggregatorRisesExceptionIfProviderClassDoesNotExist()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigAggregator(['NonExistentConfigProvider']);
    }

    public function testConfigAggregatorRisesExceptionIfProviderIsNotCallable()
    {
        $this->expectException(InvalidConfigProviderException::class);
        new ConfigAggregator([stdClass::class]);
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
        new ConfigAggregator([
            function () {
                return ['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true];
            }
        ], $this->cacheFile);
        $this->assertTrue(file_exists($this->cacheFile));
        $cachedConfig = include $this->cacheFile;
        $this->assertInternalType('array', $cachedConfig);
        $this->assertEquals(['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true], $cachedConfig);
    }

    public function testConfigAggregatorSetsDefaultModeOnCache()
    {
        touch($this->defaultFile);
        new ConfigAggregator([
            function () {
                return ['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true];
            }
        ], $this->cacheFile);
        $this->assertEquals(fileperms($this->defaultFile), fileperms($this->cacheFile));
    }

    public function testConfigAggregatorSetsModeOnCache()
    {
        new ConfigAggregator([
            function () {
                return [
                    'foo' => 'bar',
                    ConfigAggregator::ENABLE_CACHE => true,
                    ConfigAggregator::CACHE_FILEMODE => 0600
                ];
            }
        ], $this->cacheFile);
        $this->assertEquals(0600, fileperms($this->cacheFile) & 0777);
    }

    public function testConfigAggregatorSetsHandlesUnwritableCache()
    {
        chmod(dirname($this->cacheFile), 0400);

        $foo = function () {
            new ConfigAggregator([
                function () {
                    return ['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true];
                }
            ], $this->cacheFile);
        };
        @$foo(); // suppress warning

        $errors = error_get_last();
        $this->assertNotNull($errors);
        $this->assertFalse(file_exists($this->cacheFile));
    }

    public function testConfigAggregatorRespectsCacheLock()
    {
        $expected = [
            'cache' => 'locked',
            ConfigAggregator::ENABLE_CACHE => true,
        ];

        $fh = fopen($this->lockFile, 'c');
        flock($fh, LOCK_EX);
        file_put_contents($this->cacheFile, '<' . '?php return ' . var_export($expected, true) . ';');

        $method = new ReflectionMethod(ConfigAggregator::class, 'cacheConfig');
        $method->setAccessible(true);
        $method->invoke(
            new ConfigAggregator(),
            ['foo' => 'bar', ConfigAggregator::ENABLE_CACHE => true],
            $this->cacheFile
        );
        flock($fh, LOCK_UN);
        fclose($fh);

        $this->assertEquals($expected, require $this->cacheFile);
    }

    public function testConfigAggregatorCanLoadConfigFromCache()
    {
        $expected = [
            'foo' => 'bar',
            ConfigAggregator::ENABLE_CACHE => true,
        ];

        file_put_contents($this->cacheFile, '<' . '?php return ' . var_export($expected, true) . ';');

        $aggregator = new ConfigAggregator([], $this->cacheFile);
        $mergedConfig = $aggregator->getMergedConfig();

        $this->assertInternalType('array', $mergedConfig);
        $this->assertEquals($expected, $mergedConfig);
    }

    public function testConfigAggregatorRisesExceptionIfProcessorClassDoesNotExist()
    {
        $this->expectException(InvalidConfigProcessorException::class);
        new ConfigAggregator([], null, ['NonExistentConfigProcessor']);
    }

    public function testConfigAggregatorRisesExceptionIfProcessorIsNotCallable()
    {
        $this->expectException(InvalidConfigProcessorException::class);
        new ConfigAggregator([], null, [stdClass::class]);
    }

    public function testProcessorCanBeClosure()
    {
        $aggregator = new ConfigAggregator([], null, [
            function (array $config) {
                return $config + ['processor' => 'closure'];
            },
        ]);

        $config = $aggregator->getMergedConfig();
        $this->assertEquals(['processor' => 'closure'], $config);
    }

    public function testConfigAggregatorCanPostProcessConfiguration()
    {
        $aggregator = new ConfigAggregator([
            function () {
                return ['foo' => 'bar'];
            },
        ], null, [new FooPostProcessor]);
        $mergedConfig = $aggregator->getMergedConfig();

        $this->assertEquals(['foo' => 'bar', 'post-processed' => true], $mergedConfig);
    }
}
