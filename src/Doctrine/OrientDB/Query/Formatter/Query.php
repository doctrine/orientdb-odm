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
 * The aim of a Formatter class is to manipulate token values and format them
 * in order to build valid Doctrine\OrientDB's SQL expressions.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter;

use Doctrine\OrientDB\Exception;

class Query implements QueryInterface
{
    /**
     * Tokenizes a string.
     *
     * @param   string $token
     * @return  string
     */
    public static function tokenize($token)
    {
        return ":{$token}";
    }

    /**
     * Untokenizes a string.
     *
     * @param   string $token
     * @return  string
     */
    public static function untokenize($token)
    {
        return substr($token, 1);
    }

    /**
     * Strips non-SQL characters from a string leaving intact regular characters a-z and
     * integers.
     *
     * @param   string $string
     * @return  string
     */
    public static function stripNonSQLCharacters($string, $nonFilter = null)
    {
        return preg_replace("/[^\w|:|@|#|$nonFilter]/", '', $string);
    }

    /**
     * Strips non-SQL charactes from the strings contained in the the array leaving intact
     * regular characters a-z and integers.
     *
     * @param   array $strings
     * @return  array
     */
    protected static function stripNonSQLCharactersArray(array $strings, $nonFilter = null)
    {
        return array_map(function ($string) use ($nonFilter) {
            return Query::stripNonSQLCharacters($string, $nonFilter);
        }, $strings);
    }

    /**
     * Implodes and array using a comma.
     *
     * @param   array $array
     * @return  string
     */
    protected static function implode(array $array)
    {
        return implode(', ', $array);
    }

    /**
     * Implodes the $values in a string regularly formatted.
     *
     * @param   array   $values
     * @return  string
     */
    protected static function implodeRegular(array $values, $nonFilter = null)
    {
        $values = self::stripNonSQLCharactersArray($values, $nonFilter);
        $nonEmptyValues = array();

        foreach ($values as $value) {
            if ($value !== '') {
                $nonEmptyValues[] = $value;
            }
        }

        return self::implode($nonEmptyValues);
    }
}
