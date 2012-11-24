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
 * Class MapUpdates
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class MapUpdates extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        $updates = array();
        $validator = new RidValidator;

        foreach ($values as $map => $update) {
            $map = self::stripNonSQLCharacters($map);

            if ($map && is_array($update)) {
                foreach ($update as $key => $rid) {
                    $rid = $validator->check($rid, true);
                    $key = self::stripNonSQLCharacters($key);
                }

                $updates[$map] = "$map = '$key', $rid";
            }
        }

        return count($updates) ? self::implode($updates) : null;
    }
}
