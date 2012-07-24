<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Target
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Target extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $values = self::filterRegularChars($values);

        if ($count = count($values)) {
            if ($count > 1) {
                return "[" . self::implode($values) . "]";
            }

            return array_shift($values);
        }

        return null;
    }
}
