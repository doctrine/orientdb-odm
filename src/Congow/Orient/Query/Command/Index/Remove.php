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
 * This class handles the SQL statement to remove an index from the DB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Index;

use Congow\Orient\Query\Command\Index;
use Congow\Orient\Contract\Formatter\Query\Token as TokenFormatter;
use Congow\Orient\Formatter\Query\EmbeddedRid as EmbeddedRidFormatter;

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
            $method = $key ? 'andWhere' : 'where';
            $rid    = $ridFormatter::format(array($rid));
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
            'Name'  => "Congow\Orient\Formatter\Query\Regular",
        ));
    }
}
