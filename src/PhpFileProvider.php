<?php

namespace Zend\Expressive\ConfigManager;

class PhpFileProvider
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
        foreach ($this->glob($this->pattern) as $file) {
            yield include $file;
        }
    }
}
