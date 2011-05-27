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
 * Class RidUpdates
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Formatter\String;
use Orient\Contract\Formatter\Query\Token as TokenFormatter;

class RidUpdates extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $rids = array();

        foreach ($values as $key => $value) {
            $key = String::filterNonSQLChars($key);
            $rid = String::filterRid($value);

            if ($key && $rid) {
                $rids[$key] = "$key = " . $rid;
            }
        }

        return count($rids) ? self::implode($rids) : NULL;
    }
}
