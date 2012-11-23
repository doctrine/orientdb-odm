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
 * Class Between
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Formatter\Query;

use Doctrine\OrientDB\Formatter\Query;
use Doctrine\OrientDB\Formatter\String;
use Doctrine\OrientDB\Contract\Formatter\Query\Token as TokenFormatter;

class Between extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        if (count($values) === 2) {
            return "BETWEEN " . $values[0] . " AND " . $values[1];
        }

        return null;
    }
}
