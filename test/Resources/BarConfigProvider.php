<?php

namespace ZendTest\Expressive\ConfigManager\Resources;

use ArrayObject;

class BarConfigProvider
{
    /**
     * @return array|ArrayObject
     */
    public function __invoke()
    {
        return ['bar' => 'bat'];
    }
}
