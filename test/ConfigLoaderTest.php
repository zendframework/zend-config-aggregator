<?php

namespace ZendTest\Expressive\ConfigManager;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use StdClass;
use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Stdlib\Glob;
use ZendTest\Expressive\ConfigManager\Resources\BarConfigProvider;
use ZendTest\Expressive\ConfigManager\Resources\FooConfigProvider;

class ConfigManagerTest extends PHPUnit_Framework_TestCase
{
    public function testConfigManagerRisesExceptionIfProviderClassDoesNotExist()
    {
        $this->setExpectedException(RuntimeException::class);
        new ConfigManager([], [NonExistentConfigProvider::class]);
    }

    public function testConfigManagerRisesExceptionIfProviderIsNotCallable()
    {
        $this->setExpectedException(RuntimeException::class);
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
        $loader = new ConfigManager([], [function () { return ['foo' => 'bar']; }]);
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar'], (array)$config);
    }

    public function testConfigManagerMergesConfigFromFiles()
    {
        $loader = new ConfigManager(
            Glob::glob('test/Resources/config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE)
        );
        $config = $loader->getMergedConfig();
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], (array)$config);
    }
}
