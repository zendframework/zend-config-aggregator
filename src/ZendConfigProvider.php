<?php

namespace Zend\Expressive\ConfigManager;

use Zend\Config\Factory as ConfigFactory;
use Zend\Stdlib\Glob;

class ZendConfigProvider
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
        $files = Glob::glob($this->pattern, Glob::GLOB_BRACE);
        return ConfigFactory::fromFiles($files);
    }
}
