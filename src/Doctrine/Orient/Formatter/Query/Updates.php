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
 * Class Updates
 *
 * @package     Doctrine\Orient
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Formatter\Query;

use Doctrine\Orient\Formatter\Query;
use Doctrine\Orient\Formatter\String;
use Doctrine\Orient\Contract\Formatter\Query\Token as TokenFormatter;

class Updates extends Query implements TokenFormatter
{
    public static function format(array $values)
    {
        $string = "";

        foreach ($values as $key => $update) {
            if ($key = String::filterNonSQLChars($key)) {
                $string .= ' ' . $key . ' = "' . addslashes($update) . '",';
            }
        }

        return substr($string, 0, strlen($string) - 1);
    }
}
