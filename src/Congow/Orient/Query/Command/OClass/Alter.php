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
 * Command used to alter a class's native properties (eg. name, superclass).
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\OClass;

use Congow\Orient\Query\Command\OClass;

class Alter extends OClass
{
    const SCHEMA = "ALTER CLASS :Class :Attribute :Value";

    /**
     * Sets the $class to alter, setting the $attribute to its new $value.
     *
     * @param string $class
     * @param string $attribute
     * @param string $value
     */
    public function __construct($class, $attribute, $value)
    {
        parent::__construct($class);

        $this->setToken('Attribute', $attribute);
        $this->setToken('Value', $value);
    }

    /**
     * Returns the formatters for this query tokens
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array(
            'Class'         => "Congow\Orient\Formatter\Query\Regular",
            'Attribute'     => "Congow\Orient\Formatter\Query\Regular",
            'Value'         => "Congow\Orient\Formatter\Query\Regular",
        );
    }
}

