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
 * The token interface exposes a single method in order to format any type
 * of Doctrine\OrientDB token type.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Formatter
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter\Query;

interface TokenInterface
{
    /**
     * Formats the token according to the implementer class' internal rules.
     *
     * @param   array   $values
     * @return  string
     */
    public static function format(array $values);
}
