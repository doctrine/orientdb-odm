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
 * This class lets you build a SQL statements to find references of a record
 * in the DB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Reference;

use Congow\Orient\Contract\Query\Command\Reference\Find as FindInterface;
use Congow\Orient\Query\Command;

class Find extends Command implements FindInterface
{
    /**
     * Creates a new object, setting the $rid to lookup.
     *
     * @param string $rid
     */
    public function __construct($rid)
    {
        parent::__construct();

        $this->setRid($rid);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "FIND REFERENCES :Rid :ClassList";
    }

    /**
     * Restricts the classes to look into to find the record.
     *
     * @param   array $classes
     * @param   boolean $append
     * @return  Find
     */
    public function in(array $classes, $append = true)
    {
        $this->setTokenValues('ClassList', $classes, $append);

        return $this;
    }

    /**
     * Sets the $rid to look for.
     *
     * @param string $rid
     */
    protected function setRid($rid)
    {
        $this->setToken('Rid', $rid);
    }

    /**
     * Returns the formatters for this query's tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'ClassList'    => "Congow\Orient\Formatter\Query\ClassList",
        ));
    }
}
