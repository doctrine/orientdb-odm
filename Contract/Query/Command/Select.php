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
 * Select interface is responsible to define which methods should be implemented
 * by a class responsibl of generating SELECT SQL statements in OrientDB.
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Query\Command;

interface Select
{
    /**
     * Istantiates a new object setting the classes or records to look for, with
     * the $target parameter, which accepts OrientDB classes or RIDs.
     *
     * @param   array $target
     */
    public function __construct(array $target);

    /**
     * Converts the "normal" select into an index one.
     * Index selects can query with the BETWEEN operator:
     * <code>select from index:name where x between 10.3 and 10.7</code>
     *
     * @param   string $key
     * @param   string $left
     * @param   string $right
     * @return  Select
     * @todo    data filtering here, need to delegate to a formatter
     */
    public function between($key, $left, $right);

    /**
     * Sets the fields to select within the query ($projections).
     * Values can be appended through the $append parameter.
     *
     * @param   array   $projections
     * @param   boolean $append
     * @return  Select
     */
    public function select(array $projections, $append);

    /**
     * Sets an orderBy part of the SELECT.
     * The $order should contain the property and the ASC/DESC parameter, in a
     * single string: <code>name DESC</code>.
     * With $append you can decide not to override previously-set orders.
     * You can decide wheter to put the $order at $first place among the set
     * orders.
     *
     * @param   string  $order
     * @param   boolean $append
     * @param   boolean $first
     * @return  Select
     */
    public function orderBy($order, $append, $first);

    /**
     * Sets the maximum amount of records retrieved whithin the current SELECT.
     *
     * @param   integet $limit
     * @return  Select
     */
    public function limit($limit);

    /**
     * Sets the range of the SELECT, expressed in a pair of RIDs.
     * This means that the SELECT will fetch records with RID starting from
     * $left and ending before $right.
     *
     * @param   string  $left
     * @param   string  $right
     * @return  Select
     */
    public function range($left, $right);
}
