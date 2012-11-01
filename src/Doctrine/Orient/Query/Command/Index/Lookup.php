<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class handles the SQL statement to lookup an index into the DB.
 *
 * @package    Doctrine\Orient
 * @subpackage Query
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\Orient\Query\Command\Index;

use Doctrine\Orient\Query\Command\Index;
use Doctrine\Orient\Query\Command;

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
            'Index' => "Doctrine\Orient\Formatter\Query\Regular",
        ));
    }
}
