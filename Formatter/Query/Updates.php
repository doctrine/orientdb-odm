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
 * Class Updates
 *
 * @package     Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter\Query;

use Orient\Formatter\Query;
use Orient\Formatter\String;
use Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Updates extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $string = "";

        foreach ($values as $key => $update) {
            $key = String::filterNonSQLChars($key);

            if ($key) {
                $string .= ' ' . $key . ' = "' . addslashes($update) . '",';
            }
        }

        return substr($string, 0, strlen($string) - 1);
    }
}
