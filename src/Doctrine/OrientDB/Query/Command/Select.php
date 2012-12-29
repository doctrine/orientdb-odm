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
 * Select class, to build SELECT commands for Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

use Doctrine\OrientDB\Exception\Query\Command as CommandException;
use Doctrine\OrientDB\Query\Command;

class Select extends Command implements SelectInterface
{
    /**
     * Builds a Select object injecting the $target into the FROM clause.
     *
     * @param array $target
     */
    public function __construct(array $target = null)
    {
        parent::__construct();

        if ($target) {
            $this->setTokenValues('Target', $target);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "SELECT :Projections FROM :Target :Where :Between :OrderBy :Skip :Limit";
    }

    /**
     * Converts the "normal" select into an index one.
     * Index selects can query with the BETWEEN operator:
     * <code>select from index:name where x between 10.3 and 10.7</code>
     *
     * @param   string $key
     * @param   string $left
     * @param   string $right
     * @return  Select
     */
    public function between($key, $left, $right)
    {
        $this->resetWhere();
        $this->where($key);
        $this->setTokenValues('Between', array($left, $right));

        return $this;
    }

    /**
     * Deletes all the WHERE and BETWEEN conditions in the current SELECT.
     *
     * @return true
     */
    public function resetWhere()
    {
        parent::resetWhere();

        $this->clearToken('Between');

        return true;
    }

    /**
     * Sets the fields to select.
     *
     * @param array   $projections
     * @param boolean $append
     */
    public function select(array $projections, $append = true)
    {
        $this->setTokenValues('Projections', $projections, $append);

        return $this;
    }

    /**
     * Orders the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     */
    public function orderBy($order, $append = true, $first = false)
    {
        $this->setToken('OrderBy', $order, $append, $first);

        return $this;
    }

    /**
     * Sets a limit to the SELECT.
     *
     * @param integer $limit
     */
    public function limit($limit)
    {
        $this->setToken('Limit', (int) $limit);

        return $this;
    }

    /**
     * Sets the number of records to skip.
     *
     * @param integer $limit
     */
    public function skip($records)
    {
        $this->setToken('Skip', (int) $records);

        return $this;
    }

    /**
     * Returns the formatters for this query tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Projections' => "Doctrine\OrientDB\Query\Formatter\Query\Select",
            'OrderBy'     => "Doctrine\OrientDB\Query\Formatter\Query\OrderBy",
            'Limit'       => "Doctrine\OrientDB\Query\Formatter\Query\Limit",
            'Skip'        => "Doctrine\OrientDB\Query\Formatter\Query\Skip",
            'Between'     => "Doctrine\OrientDB\Query\Formatter\Query\Between",
        ));
    }
}
