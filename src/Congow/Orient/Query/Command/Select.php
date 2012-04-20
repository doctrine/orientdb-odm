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
 * Select class, to build SELECT commands for Congow\OrientDB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command;

use Congow\Orient\Exception\Query\Command as CommandException;
use Congow\Orient\Contract\Query\Formatter;
use Congow\Orient\Query\Command;
use Congow\Orient\Contract\Query\Command\Select as SelectInterface;

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
        return "SELECT :Projections FROM :Target :Where :Between :OrderBy :Limit :Range";
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
     * Adds the range to the select.
     *
     * @param integer $limit
     */
    public function range($left = null, $right = null)
    {
        $range = array();
        $params = array('left', 'right');

        foreach ($params as $param) {
            if ($$param) {
                $range[$param] = $$param;
            } elseif ($$param === false) {
                $range[$param] = null;
            }
        }

        $this->setTokenValues('Range', $range);

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
            'Projections' => "Congow\Orient\Formatter\Query\Select",
            'OrderBy'     => "Congow\Orient\Formatter\Query\OrderBy",
            'Limit'       => "Congow\Orient\Formatter\Query\Limit",
            'Range'       => "Congow\Orient\Formatter\Query\Range",
            'Between'     => "Congow\Orient\Formatter\Query\Between",
        ));
    }
}
