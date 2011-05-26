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
 * Utility class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter;

class String
{
    public function filterNonSQLChars($string, $nonFilter = NULL)
    {
        $pattern = "/[^a-z|A-Z|0-9|:|@|#|$nonFilter]/";

        return preg_replace($pattern, "", $string);
    }

    /**
     * Checks wheter the given $rid is wellformed.
     *
     * @param   string $rid
     * @return  the rid is wellformed, false otherwise
     */
    public function filterRid($rid)
    {
        $parts = explode(':', $rid);

        if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
            return $rid;
        }

        return false;
    }

    /**
     * Removes whitespaces from the beginning and the end of the $text.
     *
     * @param   string $text
     * @return  string
     */
    public function btrim($text)
    {
        return rtrim(ltrim($text));
    }
}
