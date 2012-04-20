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
 * This class handles the SQL statement to lookup an index into the DB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\Query\Command\Index;

use Congow\Orient\Query\Command\Index;
use Congow\Orient\Query\Command;

class Lookup extends Index
{
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
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "SELECT FROM index::Index :Where";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Index' => "Congow\Orient\Formatter\Query\Regular",
        ));
    }
}
