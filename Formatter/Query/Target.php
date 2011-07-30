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
 * Class Target
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Target extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $values = self::filterRegularChars($values);
        $count = count($values);

        if ($count) {
            if ($count > 1) {
                return "[" . self::implode($values) . "]";
            }

            return array_shift($values);
        }

        return null;
    }
}
