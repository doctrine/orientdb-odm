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
 * Class Range
 *
 * @package     Congow\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter\Query;

use Congow\Orient\Formatter\Query;
use Congow\Orient\Formatter\String;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Values extends Query implements TokenFormatter
{
    public static function format(array $values)
    {   
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                if (count($value) > 1) {
                    $values[$key] = "[" . addslashes(self::implode($value)) . "]";
                } else {
                    $values[$key] = addslashes(array_shift($value));
                }
            } else {
                $values[$key] = '"' . addslashes($value) . '"';
            }
        }

        return count($values) ? self::implode($values) : null;
    }
}
