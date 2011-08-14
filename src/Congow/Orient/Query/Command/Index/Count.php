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
 * This class handles counting elements in an index.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Index;

use Congow\Orient\Query\Command\Index;
use Congow\Orient\Query\Command;

class Count extends Index
{
    const SCHEMA = "SELECT count(*) AS size from index::Name";

    /**
     * Sets the $property to index.
     * Optionally, you can specify the property $class and the $type of the
     * index.
     *
     * @param string $property
     * @param string $class
     * @param string $type
     */
    public function __construct($indexName)
    {
        parent::__construct();

        $this->setToken('Name', $indexName);
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Name'  => 'Congow\Orient\Formatter\Query\Regular',
        ));
    }
}
