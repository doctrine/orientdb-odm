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

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;

class Range extends Query implements TokenInterface
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
