<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class lets you build SQL statements to create a property in a class.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Property;

use Orient\Query\Command\Property;

class Create extends Property
{
    const SCHEMA = "CREATE PROPERTY :Class.:Property :Type :Linked";

    /**
     * Generates a valid SQL statements to add $property of type $type
     * linked to $linked.
     *
     * @param string $property
     * @param string $type
     * @param string $linked
     */
    public function __construct($property, $type = NULL, $linked = NULL)
    {
        parent::__construct($property);

        $this->setToken('Type', $type);
        $this->setToken('Linked', $linked);
    }

    /**
     * Returns the formatters for this query tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Linked'    => "Orient\Formatter\Query\Regular",
        ));
    }
}
