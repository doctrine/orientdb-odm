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
 * This interface defines the mothods a Query formatter should implement
 * in order to manipulate Query strings.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Formatter;

interface QueryInterface
{
    /**
     * Tokenizes the given string: since the SQL statements are managed
     * with some tokens you need to decide how to distinguish a common word from
     * a token.
     * In <code>SELECT ALL FROM ~table</code> the ~-prefixed string is a token,
     * for example, and the tokenizer should generate an ~-prefixed token from
     * the given $key.
     * Doctrine\OrientDB uses double-colon by default.
     *
     * @param   string $key
     * @return  string
     */
    public static function tokenize($key);

    /**
     * Untokenizes a token: converts a token to a plain string.
     *
     * @param   string $token
     * @return  string
     */
    public static function untokenize($token);
}
