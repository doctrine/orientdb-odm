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
 * This interface is responsible of general-purpose manipulation of strings.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Formatter;

interface String
{
    /**
     * Converts a filesystem path into a class name.
     *
     * @param   string $file
     * @param   string $namespace
     * @return  string
     */
    public static function convertPathToClassName($file, $namespace = '');

    /**
     * Cleans the input $string removing all the characters not allowed in
     * OrientDB SQL statements.
     *
     * @param   string $string
     * @param   string $nonFilter
     * @return  string
     */
    public static function filterNonSQLChars($string, $nonFilter = null);

    /**
     * Checks wheter the given $rid is wellformed.
     *
     * @param   string $rid
     * @return  the rid is wellformed, false otherwise
     */
    public static function filterRid($rid);
}
