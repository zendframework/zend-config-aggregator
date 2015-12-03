<?php

namespace Zend\Expressive\ConfigManager;

use ArrayObject;

interface ConfigProviderInterface
{
    /**
     * @return array|ArrayObject
     */
    public function getConfig();
}
