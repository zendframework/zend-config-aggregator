<?php

namespace ZendTest\Expressive\ConfigManager;

use PHPUnit_Framework_TestCase;
use Zend\Expressive\ConfigManager\ZendConfigProvider;
use Zend\Stdlib\ArrayUtils;

class ZendConfigProviderTest extends PHPUnit_Framework_TestCase
{
    public function testProviderLoadsConfigFromFiles()
    {
        $provider = new ZendConfigProvider(__DIR__ . '/Resources/zend-config/config.*');
        $config = $provider();
        $this->assertEquals(
            [
                'database' => [
                    'adapter' => 'pdo',
                    'host' => 'db.example.com',
                    'database' => 'dbproduction',
                    'user' => 'dbuser',
                    'password' => 'secret'
                ]
            ],
            $config
        );
    }
}
