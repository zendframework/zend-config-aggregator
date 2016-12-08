<?php
/**
 * @see       https://github.com/zendframework/zend-config-aggregator for the canonical source repository
 * @copyright Copyright (c) 2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @copyright Copyright (c) 2015-2016 Mateusz Tymek (http://mateusztymek.pl)
 * @license   https://github.com/zendframework/zend-config-aggregator/blob/master/LICENSE.md New BSD License
 */

namespace Zend\ConfigAggregator;

use Zend\Stdlib\Glob;

/**
 * Helper trait used in config providers that require globbing.
 */
trait GlobTrait
{
    /**
     * Return a set of filesystem items based on a glob pattern.
     *
     * Uses the zend-stdlib Glob class for cross-platform globbing, when
     * present, falling back to glob() otherwise.
     *
     * @param string $pattern
     * @return array
     */
    private function glob($pattern)
    {
        if (class_exists(Glob::class)) {
            return Glob::glob($pattern, Glob::GLOB_BRACE);
        }

        return glob($pattern, GLOB_BRACE);
    }
}
