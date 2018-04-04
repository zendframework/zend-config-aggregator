<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregator;

use RuntimeException;

use function sprintf;

class InvalidConfigProcessorException extends RuntimeException
{

    /**
     * @param string $processor
     *
     * @return InvalidConfigProcessorException
     */
    public static function fromNamedProcessor($processor)
    {
        return new self(sprintf(
            'Cannot use %s as processor - class cannot be loaded.',
            $processor
        ));
    }

    /**
     * @param string $type
     *
     * @return InvalidConfigProcessorException
     */
    public static function fromUnsupportedType($type)
    {
        return new self(sprintf(
            'Cannot use processor of type %s as processor - config processor must be callable.',
            $type
        ));
    }
}
