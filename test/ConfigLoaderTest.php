<?php

namespace ZendTest\Expressive\ConfigManager;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use StdClass;
use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Expressive\ConfigManager\InvalidConfigProviderException;
use Zend\Stdlib\Glob;
use ZendTest\Expressive\ConfigManager\Resources\BarConfigProvider;
use ZendTest\Expressive\ConfigManager\Resources\FooConfigProvider;

class ConfigManagerTest extends PHPUnit_Framework_TestCase
{
    public function testConfigManagerRisesExceptionIfProviderClassDoesNotExist()
    {
        $this->setExpectedException(InvalidConfigProviderException::class);
        new ConfigManager([], [NonExistentConfigProvider::class]);
    }

    public function testConfigManagerRisesExceptionIfProviderIsNotCallable()
    {
        $this->setExpectedException(InvalidConfigProviderException::class);
        new ConfigManager([], [StdClass::class]);
    }

    public function testConfigManagerMergesConfigFromProviders()
    {
        $loader = new ConfigManager([], [FooConfigProvider::class, BarConfigProvider::class]);
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bat'], (array)$config);
    }

    public function testProviderCanBeClosure()
    {
        $loader = new ConfigManager(
            [],
            [
                function () {
                    return ['foo' => 'bar'];
                }
            ]
        );
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar'], (array)$config);
    }

    public function testProviderCanBeGenerator()
    {
        $loader = new ConfigManager(
            [],
            [
                function () {
                    yield ['foo' => 'bar'];
                    yield ['baz' => 'bat'];
                }
            ]
        );
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'baz' => 'bat'], (array)$config);
    }

    public function testConfigManagerMergesConfigFromFiles()
    {
        $loader = new ConfigManager(
            Glob::glob('test/Resources/config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE)
        );
        $config = $loader->getMergedConfig();
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], (array)$config);
    }

    public function testConfigManagerCanCacheConfig()
    {
        $cacheFile = tempnam(sys_get_temp_dir(), 'expressive_config_loader');
        unlink($cacheFile);
        new ConfigManager(
            [],
            [
                function () {
                    return ['foo' => 'bar', 'config_cache_enabled' => true];
                }
            ],
            $cacheFile
        );
        $this->assertTrue(is_file($cacheFile));
        $cachedConfig = include $cacheFile;
        $this->assertInternalType('array', $cachedConfig);
        $this->assertEquals(['foo' => 'bar', 'config_cache_enabled' => true], $cachedConfig);
        unlink($cacheFile);
    }

    public function testConfigManagerCanLoadConfigFromCache()
    {
        $cacheFile = tempnam(sys_get_temp_dir(), 'expressive_config_loader');
        file_put_contents(
            $cacheFile,
            '<?php return ' . var_export(['foo' => 'bar', 'config_cache_enabled' => true], true) . ";\n"
        );
        $configManager = new ConfigManager(
            [],
            [],
            $cacheFile
        );
        $this->assertTrue(is_file($cacheFile));
        $cachedConfig = $configManager->getMergedConfig();
        $this->assertInstanceOf(ArrayObject::class, $cachedConfig);
        $this->assertEquals(['foo' => 'bar', 'config_cache_enabled' => true], (array)$cachedConfig);
        unlink($cacheFile);
    }
}
