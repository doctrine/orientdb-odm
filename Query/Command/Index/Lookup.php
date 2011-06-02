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
 * This class handles the SQL statement to lookup an index into the DB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command\Index;
use Orient\Query\Command;

class Lookup extends Index
{
    const SCHEMA = "SELECT FROM index::Index :Where";

    /**
     * Builds a new statement, setting the $index to lookup.
     *
     * @param string $index
     */
    public function __construct($index)
    {
        parent::__construct();

        $this->setToken('Index', $index);
    }

    /**
     * Returns the formatters for this query tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Index' => "Orient\Formatter\Query\Regular",
        ));
    }
}
