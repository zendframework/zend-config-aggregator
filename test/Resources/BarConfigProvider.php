<?php

namespace ZendTest\Expressive\ConfigManager\Resources;

use ArrayObject;
use Zend\Expressive\ConfigManager\ConfigProviderInterface;

class BarConfigProvider implements ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig()
    {
        return ['bar' => 'bat'];
    }
}
