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
 * This class manages the creation of SQL statements able to delete records
 * in a class.
 *
 * @package     Congow\Orient
 * @subpackage  Query
 * @author      Alessandro nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command;

use Congow\Orient\Query\Command;

class Delete extends Command
{
    /**
     * Builds a new statement, setting the class in which the records are gonna
     * be deleted.
     *
     * @param string $from
     */
    public function __construct($from)
    {
        parent::__construct();

        $this->setClass($from);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "DELETE FROM :Class :Where";
    }

    /**
     * Sets the query $class.
     *
     * @param string $class
     */
    protected function setClass($class)
    {
        $this->setToken('Class', $class);
    }
}
