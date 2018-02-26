<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\ConfigAggregator\Resources;

class FooPostProcessor
{

    /**
     * @param array $config
     *
     * @return array
     */
    public function __invoke(array $config)
    {
        return $config + ['post-processed' => true];
    }
}
