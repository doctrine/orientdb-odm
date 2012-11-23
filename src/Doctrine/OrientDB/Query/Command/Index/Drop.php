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
 * This class handles the SQL statements that drops an index from the DB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Index;

use Doctrine\OrientDB\Query\Command\Index;

class Drop extends Index
{
    /**
     * Creates a new statements to manage indexes on the $property of the given
     * $class.
     *
     * @param string $property
     * @param string $class
     */
    public function __construct($property, $class = null)
    {
        parent::__construct();

        if ($class) {
            $this->setToken('IndexClass', $class);
        }

        $this->setToken('Property', $property);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "DROP INDEX :IndexClass:Property";
    }
}
