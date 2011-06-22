<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class is responsible of general-purpose manipulating strings.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter;

class String
{
    /**
     * Converts a filesystem path into a class name.
     *
     * @param   string $file
     * @param   string $namespace
     * @return  string
     */
    public static function convertPathToClassName($file, $namespace = '')
    {
        $tokens     = array('.php','/', '.\\');
        $replaces   = array('','\\',$namespace);
        
        return str_replace($tokens, $replaces, $file);
    }

    /**
     * Cleans the input $string removing all the characters not allowed in
     * OrientDB SQL statements.
     *
     * @param   string $string
     * @param   string $nonFilter
     * @return  string
     */
    public static function filterNonSQLChars($string, $nonFilter = NULL)
    {
        $pattern = "/[^a-z|A-Z|0-9|:|@|#|$nonFilter]/";

        return preg_replace($pattern, "", $string);
    }

    /**
     * Checks wheter the given $rid is wellformed.
     *
     * @param   string $rid
     * @return  the rid is wellformed, false otherwise
     */
    public static function filterRid($rid)
    {
        $parts = explode(':', $rid);
        
        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return $rid;
        }
        
        return false;
    }
}
