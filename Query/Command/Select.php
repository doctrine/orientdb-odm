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
 * Select class, to build SELECT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Exception\Query\Command as CommandException;
use Orient\Contract\Query\Formatter;
use Orient\Query\Command;
use Orient\Contract\Query\Command\Select as SelectInterface;

class Select extends Command implements SelectInterface
{
    const SCHEMA =
        "SELECT :Projections FROM :Target :Where :OrderBy :Limit :Range"
    ;

    /**
     * Builds a Select object injecting the $target into the FROM clause.
     *
     * @param array $target
     */
    public function __construct(array $target = NULL)
    {
        parent::__construct();

        if ($target) {
            $this->setTokenValues('Target', $target);
        }
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
    public function range($left = NULL, $right = NULL)
    {
        $range = array();
        $params = array('left', 'right');

        foreach ($params as $param) {
            if ($$param) {
                $range[$param] = $$param;
            } elseif ($$param === false) {
                $range[$param] = NULL;
            }
        }

        $this->setTokenValues('Range', $range);

        return $this;
    }
    
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Projections' => "Orient\Formatter\Query\Regular",
            'OrderBy'     => "Orient\Formatter\Query\OrderBy",
            'Limit'       => "Orient\Formatter\Query\Limit",
            'Range'       => "Orient\Formatter\Query\Range",
        ));
    }
}
