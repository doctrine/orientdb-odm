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
 * This class lets you build SQL statements to create a property in a class.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Property;

use Congow\Orient\Query\Command\Property;

class Create extends Property
{
    /**
     * Generates a valid SQL statements to add $property of type $type
     * linked to $linked.
     *
     * @param string $property
     * @param string $type
     * @param string $linked
     */
    public function __construct($property, $type = null, $linked = null)
    {
        parent::__construct($property);
        
        if ($type) {
            $this->setType($type);
        }
        
        if ($linked) {
            $this->setLinked($linked);
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "CREATE PROPERTY :Class.:Property :Type :Linked";
    }

    public function setLinked($linked)
    {
        $this->setToken('Linked', $linked);
        
        return $this;
    }
    
    public function setType($type)
    {
        $this->setToken('Type', $type);
        
        return $this;
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Linked'    => "Congow\Orient\Formatter\Query\Regular",
        ));
    }
}
