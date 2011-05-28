<?php

/**
 * Alter class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\OClass;

use Orient\Query\Command\OClass;

class Alter extends OClass
{
    const SCHEMA = "ALTER CLASS :Class :Attribute :Value";

    public function __construct($class, $attribute, $value)
    {
        parent::__construct($class);

        $this->setToken('Attribute', $attribute);
        $this->setToken('Value', $value);
    }

    protected function getTokenFormatters()
    {
        return array(
            'Class'         => "Orient\Formatter\Query\Regular",
            'Attribute'     => "Orient\Formatter\Query\Regular",
            'Value'         => "Orient\Formatter\Query\Regular",
        );
    }
}

