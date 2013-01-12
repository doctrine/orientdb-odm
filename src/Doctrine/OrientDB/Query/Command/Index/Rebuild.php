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
 * This class handles the SQL statement to remove an index from the DB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Index;

use Doctrine\OrientDB\Query\Command\Index;

class Rebuild extends Index
{
    public function __construct($indexName)
    {
        parent::__construct();

        $this->setToken('IndexName', $indexName);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "REBUILD INDEX :IndexName";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'IndexName'  => "Doctrine\OrientDB\Query\Formatter\Query\RebuildIndex",
        ));
    }
}
