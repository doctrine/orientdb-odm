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
 * Class Target
 *
 * @package     Doctrine\OrientDB
 * @subpackage  Formatter
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;

class Target extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        $values = self::stripNonSQLCharacters($values);

        if ($count = count($values)) {
            if ($count > 1) {
                return "[" . self::implode($values) . "]";
            }

            return array_shift($values);
        }

        return null;
    }
}
