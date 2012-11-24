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
 * Class RidUpdates
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use       Doctrine\OrientDB\Query\Formatter\Query;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class RidUpdates extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        $rids = array();
        $validator = new RidValidator;

        foreach ($values as $key => $value) {
            $key = self::stripNonSQLCharacters($key);
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
