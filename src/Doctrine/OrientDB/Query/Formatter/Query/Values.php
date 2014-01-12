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
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class Values extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        foreach ($values as $key => $value) {
            $rid = false;

            if (is_string($value)) {
                $validator      = new RidValidator;

                try {
                    $rid = $validator->check($value);
                } catch (\Exception $e) {}
            }

            if ($rid) {
                $values[$key] = $value;
            } else if (is_array($value)) {
                $values[$key] = "[" . addslashes(self::implode($value)) . "]";
            } else if ($value === null) {
                $values[$key] = 'NULL';
            } else if (is_int($value) || is_float($value)) {
                $values[$key] = $value;
            } else if (is_bool($value)) {
                $values[$key] = $value ? 'TRUE' : 'FALSE';
            } else {
                $values[$key] = '"' . addslashes($value) . '"';
            }
        }

        if ($values) {
            return self::implode($values);
        }

        return null;
    }
}
