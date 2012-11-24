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
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Index;

use Doctrine\OrientDB\Query\Command\Index;
use Doctrine\OrientDB\Query\Formatter\Query\EmbeddedRid as EmbeddedRidFormatter;

class Remove extends Index
{
    public function __construct($indexName, $key, $rid = null, TokenFormatter $ridFormatter = null)
    {
        parent::__construct();

        $ridFormatter = $ridFormatter ?: new EmbeddedRidFormatter;
        $this->setToken('Name', $indexName);

        if (!is_null($key)) {
            $this->where("key = ?", $key);
        }

        if ($rid) {
            $rid = $ridFormatter::format(array($rid));
            $method = $key ? 'andWhere' : 'where';

            $this->$method("rid = $rid");
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "DELETE FROM index::Name :Where";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Name'  => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
        ));
    }
}
