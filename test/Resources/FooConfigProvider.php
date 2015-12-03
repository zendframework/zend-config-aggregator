<?php

namespace ZendTest\Expressive\ConfigManager\Resources;

use ArrayObject;
use Zend\Expressive\ConfigManager\ConfigProviderInterface;

class FooConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig()
    {
        return ['foo' => 'bar'];
    }
}
