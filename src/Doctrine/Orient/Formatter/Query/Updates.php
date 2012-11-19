<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Updates
 *
 * @package     Doctrine\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Formatter\Query;

use Doctrine\Orient\Formatter\Query;
use Doctrine\Orient\Formatter\String;
use Doctrine\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Updates extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $string = "";

        foreach ($values as $key => $value) {
            if ($key = String::filterNonSQLChars($key)) {
                if ($value === null) {
                    $value = 'NULL';
                } else if (is_int($value) || is_float($value)) {
                    // Preserve content of $value as is
                } else if (is_bool($value)) {
                    $value = $value ? 'TRUE' : 'FALSE';
                } else {
                    $value = '"' . addslashes($value) . '"';
                }

                $string .= " $key = $value,";
            }
        }

        return substr($string, 0, strlen($string) - 1);
    }
}
