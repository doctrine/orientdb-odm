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
 * Class Range
 *
 * @package     Doctrine\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Formatter\Query;

use Doctrine\Orient\Formatter\Query;
use Doctrine\Orient\Formatter\String;
use Doctrine\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Range extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $range = array();

        foreach ($values as $rid) {
            if ($value = Rid::format(array($rid))) {
                $range[] = $value;
            }
        }

        if ($range = array_slice($range, 0, 2)) {
            return "RANGE " . implode(' ', $range);
        }

        return null;
    }
}
