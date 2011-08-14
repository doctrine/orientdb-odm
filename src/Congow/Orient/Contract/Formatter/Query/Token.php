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
 * The token interface exposes a single method in order to format any type
 * of Congow\OrientDB token type.
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Formatter\Query;

interface Token
{
    /**
     * Formats the token according to the implementer class' internal rules.
     *
     * @param   array   $values
     * @return  string
     */
    public static function format(array $values);
}
