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
 * This is a central point to manipulate SQL statements dealing with updates.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Erik Weinmaster <weinmaster@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Create;

use Doctrine\OrientDB\Query\Command;
use Doctrine\OrientDB\Query\Command\UpdateInterface;

class Edge extends Command implements UpdateInterface
{
    /**
     * Builds a new statement, setting the $class.
     *
     * @param string $class
     * @param string $from
     * @param string $to
     */
    public function __construct($class, $from, $to)
    {
        parent::__construct();

        $this->setToken('Class', $class);
        $this->setToken('From', $from);
        $this->setToken('To', $to);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "CREATE EDGE :Class FROM :From TO :To SET :Fields";
    }

    /**
     * Set the $values of the updates to be done.
     * You can $append the values.
     *
     * @param  array   $values
     * @param  boolean $append
     * @return Update
     */
    public function set(array $values, $append = true)
    {
        $this->setTokenValues('Fields', $values, $append);

        return $this;
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'From'   => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'To'     => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Fields' => "Doctrine\OrientDB\Query\Formatter\Query\Updates"
        ));
    }
}
