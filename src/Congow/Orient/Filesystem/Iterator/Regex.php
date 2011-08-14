<?php

/**
 * Iterator class
 *
 * @package    Congow\Orient
 * @subpackage Filesystem
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Filesystem\Iterator;

class Regex extends \RegexIterator
{
    /**
     * Returns a regex iterator instance, starting from $dir applying the given
     * $pattern.
     *
     * @param string            $directory
     * @param regex             $pattern
     * @return \RegexIterator
     */
    public function __construct($directory, $pattern)
    {
        $iterator       = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        
        parent::__construct($iterator, $pattern);
    }
}

