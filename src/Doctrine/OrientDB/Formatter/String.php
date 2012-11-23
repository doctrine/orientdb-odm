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
 * This class is responsible of general-purpose manipulating strings.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Formatter
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Formatter;

use Doctrine\OrientDB\Validator;

class String implements StringInterface
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
        $absPath    = realpath($file);
        $namespaces = explode('/', $absPath);
        $start      = false;
        $i          = 0;
        $chunk      = explode('\\', $namespace);
        $namespace  = array_shift($chunk);

        while ($namespaces[$i] != $namespace) {
            unset($namespaces[$i]);

            if (!array_key_exists(++$i,$namespaces)) {
                break;
            }
        }

        $className = str_replace('.php', null, array_pop($namespaces));

        return '\\'. implode('\\', $namespaces) . '\\' . $className;
    }

    /**
     * Cleans the input $string removing all the characters not allowed in
     * Doctrine\OrientDB SQL statements.
     *
     * @param   string $string
     * @param   string $nonFilter
     * @return  string
     */
    public static function filterNonSQLChars($string, $nonFilter = null)
    {
        $pattern = "/[^a-z|A-Z|0-9|:|@|#|$nonFilter]/";

        return preg_replace($pattern, "", $string);
    }
}
