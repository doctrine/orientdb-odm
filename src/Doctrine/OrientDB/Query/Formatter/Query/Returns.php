<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Erik Weinmaster <weinmaster@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Return
 *
 * @package    Doctrine\OrientDB
 * @subpackage Formatter
 * @author     Erik Weinmaster <weinmaster@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

use Doctrine\OrientDB\Query\Formatter\Query;

class Returns extends Query implements TokenInterface
{
    public static function format(array $values)
    {
        return count($values) > 0 ? "RETURN " . $values[0] : null;
    }
}
