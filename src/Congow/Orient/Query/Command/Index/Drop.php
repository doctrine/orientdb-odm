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
 * This class handles the SQL statements that drops an index from the DB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Index;

use Congow\Orient\Query\Command\Index;

class Drop extends Index
{
    const SCHEMA = "DROP INDEX :IndexClass:Property";

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
}
