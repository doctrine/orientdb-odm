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
 * This class handles the SQL statement to generate an index into the DB.
 *
 * @package    Doctrine\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Query\Command\Index;

use Doctrine\Orient\Query\Command\Index;
use Doctrine\Orient\Query\Command;

class Put extends Index
{
    /**
     * Creates a new instance of this command setting the index to insert into,
     * the key of the new entry and its value, which is a RID.
     *
     * @param string $indexName
     * @param string $key
     * @param string $rid
     */
    public function __construct($indexName, $key, $rid)
    {
        parent::__construct();

        $this->setToken('Name', $indexName);
        $this->setToken('Key', $key);
        $this->setToken('Value', $rid);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "INSERT INTO index::Name (key,rid) values (\":Key\", :Value)";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Name'  => "Doctrine\Orient\Formatter\Query\Regular",
            'Key'   => "Doctrine\Orient\Formatter\Query\Regular",
            'Value' => "Doctrine\Orient\Formatter\Query\EmbeddedRid",
        ));
    }
}
