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
 * This class lets you build a SQL statements to find references of a record
 * in the DB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Reference;

use Orient\Contract\Query\Command\Reference\Find as FindInterface;
use Orient\Query\Command;

class Find extends Command implements FindInterface
{
    const SCHEMA = "FIND REFERENCES :Rid :ClassList";

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
    
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'ClassList'    => "Orient\Formatter\Query\ClassList",
        ));
    }
}
