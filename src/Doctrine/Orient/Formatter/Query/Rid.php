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
 * Class Rid
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

class Rid extends Query implements TokenFormatter
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

