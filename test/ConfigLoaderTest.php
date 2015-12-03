<?php

namespace ZendTest\Expressive\ConfigManager;

use PHPUnit_Framework_TestCase;
use Zend\Expressive\ConfigManager\ConfigManager;
use Zend\Stdlib\Glob;
use ZendTest\Expressive\ConfigManager\Resources\BarConfigProvider;
use ZendTest\Expressive\ConfigManager\Resources\FooConfigProvider;

class ConfigManagerTest extends PHPUnit_Framework_TestCase
{
    public function testConfigLoaderMergesConfigFromProviders()
    {
        $loader = new ConfigManager([], [FooConfigProvider::class, BarConfigProvider::class]);
        $config = $loader->getMergedConfig();
        $this->assertEquals(['foo' => 'bar', 'bar' => 'bat'], (array)$config);
    }

    public function testConfigLoaderMergesConfigFromFiles()
    {
        $loader = new ConfigManager(
            Glob::glob('test/Resources/config/{{,*.}global,{,*.}local}.php', Glob::GLOB_BRACE),
            []
        );
        $config = $loader->getMergedConfig();
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], (array)$config);
    }
}
