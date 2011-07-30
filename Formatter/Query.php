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
 * The aim of a Formatter class is to manipulate token values and format them
 * in order to build valid OrientDB's SQL expressions.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter;

use Orient\Exception;
use Orient\Formatter\String;
use Orient\Contract\Formatter;

class Query implements Formatter\Query
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
     * Filters the array values leaving intact regular characters a-z and
     * integers.
     *
     * @param   array $values
     * @return  array
     */
    protected static function filterRegularChars(array $values, $nonFilter = null)
    {
        return array_map(function ($arr) use ($nonFilter) {
                    return String::filterNonSQLChars($arr, $nonFilter);
                }, $values);
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
        $values = self::filterRegularChars($values, $nonFilter);
        $nonEmptyValues = array();

        foreach ($values as $value) {
            if ($value !== '') {
                $nonEmptyValues[] = $value;
            }
        }

        return self::implode($nonEmptyValues);
    }
}
