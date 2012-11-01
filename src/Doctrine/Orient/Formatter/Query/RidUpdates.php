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
 * Class RidUpdates
 *
 * @package     Doctrine\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Formatter\Query;

use Doctrine\Orient\Formatter\Query;
use Doctrine\Orient\Formatter\String;
use Doctrine\Orient\Contract\Formatter\Query\Token as TokenFormatter;
use Doctrine\Orient\Validator\Rid as RidValidator;

class RidUpdates extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $rids = array();
        $validator = new RidValidator;

        foreach ($values as $key => $value) {
            $key = String::filterNonSQLChars($key);
            $rid = $validator->check($value, true);

            if ($key && $rid) {
                $rids[$key] = "$key = $rid";
            }
        }

        if ($rids) {
            return self::implode($rids);
        }

        return null;
    }
}
