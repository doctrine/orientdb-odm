<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Range
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Formatter\String;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Range extends Query implements TokenFormatter
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

        return count($range) ? "RANGE " . implode(' ', $range) : null;
    }
}
