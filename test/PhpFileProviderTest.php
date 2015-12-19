<?php

namespace ZendTest\Expressive\ConfigManager;

use PHPUnit_Framework_TestCase;
use Zend\Expressive\ConfigManager\PhpFileProvider;
use Zend\Stdlib\ArrayUtils;

class PhpFileProviderTest extends PHPUnit_Framework_TestCase
{
    public function testProviderLoadsConfigFromFiles()
    {
        $provider = new PhpFileProvider(__DIR__ . '/Resources/config/{{,*.}global,{,*.}local}.php');
        $merged = [];
        foreach ($provider() as $item) {
            $merged = ArrayUtils::merge($merged, $item);
        }
        $this->assertEquals(['fruit' => 'banana', 'vegetable' => 'potato'], $merged);
    }
}
