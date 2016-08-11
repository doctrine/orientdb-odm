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
 * Class Updates
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;

class Updates extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        $string = "";

        foreach ($values as $key => $value) {
            if ($key = self::stripNonSQLCharacters($key)) {
                if ($value === null) {
                    $value = 'NULL';
                } else if (is_int($value) || is_float($value)) {
                    // Preserve content of $value as is
                } else if (is_bool($value)) {
                    $value = $value ? 'TRUE' : 'FALSE';
                } elseif(is_array($value)) {
                    $value = '[' . implode(',', $value) . ']';
                } else {
                    $value = '"' . addslashes($value) . '"';
                }

                $string .= " $key = $value,";
            }
        }

        return substr($string, 0, strlen($string) - 1);
    }
}
