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
 * Class Range
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Formatter\Query;

use Doctrine\OrientDB\Formatter\Query;
use Doctrine\OrientDB\Formatter\String;
use Doctrine\OrientDB\Contract\Formatter\Query\Token as TokenFormatter;

class Values extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                if (count($value) > 1) {
                    $values[$key] = "[" . addslashes(self::implode($value)) . "]";
                } else {
                    $values[$key] = addslashes(array_shift($value));
                }
            } else if ($value === null) {
                $values[$key] = 'NULL';
            } else if (is_int($value) || is_float($value)) {
                $values[$key] = $value;
            } else if (is_bool($value)) {
                $values[$key] = $value ? 'TRUE' : 'FALSE';
            } else {
                $values[$key] = '"' . addslashes($value) . '"';
            }
        }

        if ($values) {
            return self::implode($values);
        }

        return null;
    }
}
