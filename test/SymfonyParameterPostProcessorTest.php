<?php

namespace ZendTest\ConfigAggregator;

use PHPUnit\Framework\TestCase;
use Zend\ConfigAggregator\SymfonyParameterPostProcessor;

class SymfonyParameterPostProcessorTest extends TestCase
{

    public function testProcessorConvertsParametersIntoTheirValues()
    {
        $parameters = ['foo' => 'bar', 'bar' => true, 'baz' => 1];
        $processor = new SymfonyParameterPostProcessor($parameters);

        $config = [
            'qux' => 'bar',
            'quxx' => '%foo%',
            'recursive' => [
                'foo' => '%bar%',
                'url' => 'https://example.com/?foo=bar&question=42%%3F',
            ],
            'mybaz' => '%baz%',
        ];

        $processed = $processor($config);

        $subset = array_replace_recursive($config, [
            'quxx' => 'bar',
            'recursive' => [
                'foo' => true,
                'url' => 'https://example.com/?foo=bar&question=42%3F',
            ],
            'mybaz' => 1,
        ]);
        $this->assertArraySubset($subset, $processed);
    }

    public function testProcessorAddsParametersToConfiguration()
    {
        $parameters = ['foo' => 'bar'];

        $processor = new SymfonyParameterPostProcessor($parameters);
        $config = [];

        $processed = $processor($config);
        $this->assertArrayHasKey('parameters', $processed);
        $this->assertEquals($parameters, $processed['parameters']);
    }

    public function testProcessorCanHandleEscapedVariables()
    {
        $parameters = ['foo' => 'bar'];
        $processor = new SymfonyParameterPostProcessor($parameters);

        $config = ['key' => '%%foo%%'];

        $processed = $processor($config);
        $this->assertArraySubset(['key' => '%foo%'], $processed);
    }
}
