<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This interface is responsible of general-purpose manipulation of strings.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter;

interface StringInterface
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
     * Doctrine\OrientDB SQL statements.
     *
     * @param   string $string
     * @param   string $nonFilter
     * @return  string
     */
    public static function filterNonSQLChars($string, $nonFilter = null);
}
