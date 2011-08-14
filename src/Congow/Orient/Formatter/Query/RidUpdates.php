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
 * Class RidUpdates
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Formatter\String;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

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

        return count($rids) ? self::implode($rids) : null;
    }
}
