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
 * This class handles the SQL statement to generate an index into the DB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Index;

use Doctrine\OrientDB\Query\Command\Index;
use Doctrine\OrientDB\Query\Command;

class Create extends Index
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
    public function __construct($property, $type, $class = null)
    {
        parent::__construct();

        if ($class) {
            $this->setToken('IndexClass', $class);
        }

        $this->type($type);
        $this->setToken('Property', $property);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "CREATE INDEX :IndexClass:Property :Type";
    }

    /**
     * Sets the type of the index to create.
     *
     * @param   string $type
     * @return  Create
     */
    public function type($type)
    {
        $this->setToken('Type', $type);

        return $this;
    }
}
