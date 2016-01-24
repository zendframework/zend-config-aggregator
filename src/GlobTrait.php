<?php

namespace Zend\Expressive\ConfigManager;

use Zend\Stdlib\Glob;

/**
 * Helper trait used in config providers that require globbing.
 */
trait GlobTrait
{
    private function glob($pattern)
    {
        if (class_exists(Glob::class)) {
            return Glob::glob($pattern, Glob::GLOB_BRACE);
        }

        return glob($pattern, GLOB_BRACE);
    }
}
