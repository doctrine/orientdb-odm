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
 * Class Rid
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Formatter\String;

class Rid extends Query
{
    public static function format(array $values)
    {
        $values = array_filter($values, function ($arr) {
                    return String::filterRid($arr);
                });

        return (count($values)) ? array_shift($values) : NULL;
    }
}
