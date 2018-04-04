<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregator;

use RuntimeException;

use function sprintf;

class InvalidConfigProviderException extends RuntimeException
{

    /**
     * @param string $provider
     *
     * @return InvalidConfigProviderException
     */
    public static function fromNamedProvider($provider)
    {
        return new self(sprintf(
            'Cannot read config from %s - class cannot be loaded.',
            $provider
        ));
    }

    /**
     * @param string $type
     *
     * @return InvalidConfigProviderException
     */
    public static function fromUnsupportedType($type)
    {
        return new self(
            sprintf("Cannot read config from %s - config provider must be callable.", $type)
        );
    }
}
