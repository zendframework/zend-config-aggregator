<?php

namespace ZendTest\Expressive\ConfigManager\Resources;

use ArrayObject;

class FooConfigProvider
{
    /**
     * @return array|ArrayObject
     */
    public function __invoke()
    {
        return ['foo' => 'bar'];
    }
}
