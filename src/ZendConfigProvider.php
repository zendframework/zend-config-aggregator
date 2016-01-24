<?php

namespace Zend\Expressive\ConfigManager;

use Zend\Config\Factory as ConfigFactory;

class ZendConfigProvider
{
    use GlobTrait;

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
        $files = $this->glob($this->pattern);
        return ConfigFactory::fromFiles($files);
    }
}
