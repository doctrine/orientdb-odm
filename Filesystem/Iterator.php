<?php

/**
 * Iterator class
 *
 * @package    Orient
 * @subpackage Filesystem
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Filesystem;

class Iterator
{
    /**
     * Returns a regex iterator instance, starting from $dir applying the given
     * $pattern.
     *
     * @param string            $directory
     * @param regex             $pattern
     * @return \RegexIterator
     */
    public static function getRegexIterator($directory, $pattern)
    {
        $iterator       = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        
        return new \RegexIterator($iterator, $pattern);
    }
}

