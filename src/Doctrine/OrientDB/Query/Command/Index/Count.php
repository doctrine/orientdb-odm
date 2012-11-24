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
 * This class handles counting elements in an index.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Index;

use Doctrine\OrientDB\Query\Command\Index;
use Doctrine\OrientDB\Query\Command;

class Count extends Index
{
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
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "SELECT count(*) AS size from index::Name";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Name'  => 'Doctrine\OrientDB\Query\Formatter\Query\Regular',
        ));
    }
}
