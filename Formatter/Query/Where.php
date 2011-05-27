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
 * Class Where
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Where extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $values = self::implode($values);
        $values = str_replace(", AND", " AND", $values);
        $values = str_replace(", OR", " OR", $values);

        return $values;
    }
}
