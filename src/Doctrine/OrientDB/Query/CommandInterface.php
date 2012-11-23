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
 * Command interface, a common interface for all the SQL commands executable
 * in Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query;

interface CommandInterface
{
    /**
     * Sets a where token using the AND operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param   string $condition
     * @param   string $value
     * @return  Command
     */
    public function andWhere($condition, $value = null);

    /**
     * Sets the FROM clause of a SQL statement, injecting an array of $target
     * and deciding to remove previously set targets or not with the $append
     * parameter.
     *
     * @param   array   $target
     * @param   boolean $append
     * @return  Command
     */
    public function from(array $target, $append = true);

    /**
     * Returns the SQL generated within this command, replacing the tokens in
     * the schema with their actual values.
     *
     * @return  string
     */
    public function getRaw();

    /**
     * Analizying the class SCHEMA, it returns an array containing all the
     * tokens found there.
     *
     * @return   array
     */
    public function getTokens();

    /**
     * Returns the value of the given $token.
     * Token values are always expressed as a series of values in an array, also
     * if the token does not support multiple values.
     * For example, the WHERE condition supports multiple values:
     * <code>WHERE val1 = x AND val2 = y OR val3 = z</code>
     * while the LIMIT clause not:
     * <code>LIMIT 20</code>
     * However, both those tokens values are an array: this is done to
     * internally simplify things.
     *
     * @param   string  $token
     * @return  array
     */
    public function getTokenValue($token);

    /**
     * Sets a where token using the OR operator.
     * If the $condition contains a "?", it will be replaced by the $value.
     *
     * @param   string $condition
     * @param   string $value
     * @return  Command
     */
    public function orWhere($condition, $value = null);

    /**
     * Deletes all the WHERE conditions in the current command.
     *
     * @return true
     */
    public function resetWhere();

    /**
     * Sets a WHERE condition for the current query.
     * You can set the $condition using a trailing question mark, that will be
     * replaced and safely quoted with the $value.
     * Where conditions can be nested using the $append parameter and pre-fixed
     * with the right $clause preposition (WHERE, AND, OR).
     *
     * @param   string  $condition
     * @param   mixed   $value
     * @param   boolean $append
     * @param   string  $clause
     */
    public function where($condition, $value = null, $append = false, $clause = "WHERE");
}
