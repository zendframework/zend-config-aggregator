<?php

namespace Zend\Expressive\ConfigManager;

use Zend\Stdlib\Glob;

class GlobFileProvider
{
    /** @var string */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function __invoke()
    {
        foreach (Glob::glob($this->pattern, Glob::GLOB_BRACE) as $file) {
            yield include $file;
        }
    }
}
