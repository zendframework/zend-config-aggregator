<?php

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
