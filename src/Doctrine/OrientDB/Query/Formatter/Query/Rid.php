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
 * Class Rid
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class Rid extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        $validator = new RidValidator();

        $filterCallback = function ($arr) use ($validator) {
            return $validator->check($arr, true);
        };

        if ($values = array_filter($values, $filterCallback)) {
            return array_shift($values);
        }

        return null;
    }
}

