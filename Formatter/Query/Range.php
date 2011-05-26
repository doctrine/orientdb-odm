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
 * Class Range
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Formatter\String;

class Range extends Query
{
    public static function format(array $values)
    {
        $range = array();

        foreach ($values as $rid) {
            $value = Rid::format(array($rid));

            if ($value) {
                $range[] = $value;
            }
        }

        $range = array_slice($range, 0, 2);

        return count($range) ? "RANGE " . self::implode($range) : NULL;
    }
}
