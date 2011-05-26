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
 * Class Limit
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Formatter\String;

class Limit extends Query
{
    public static function format(array $values)
    {
        $values = function () use ($values) {
                    foreach ($values as $limit) {
                        if (is_numeric($limit)) {
                            return $limit;
                        }
                    }

                    return false;
                };

        return $values() ? "LIMIT " . $values() : NULL;
    }
}
