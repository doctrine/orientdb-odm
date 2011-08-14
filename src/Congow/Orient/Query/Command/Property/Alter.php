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
 * Command used to alter properties in a class.
 *
 * @package
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Property;

use Congow\Orient\Query\Command\Property;

class Alter extends Property
{
    const SCHEMA = "ALTER PROPERTY :Class.:Property :Attribute :Value";

    /**
     * Sets the $attribute to change and its new $value.
     *
     * @param   string $attribute
     * @param   string $value
     * @return  Alter
     */
    public function changing($attribute, $value)
    {
        $this->setToken('Attribute', $attribute);
        $this->setToken('Value', $value);

        return $this;
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array(
            'Class'         => "Congow\Orient\Formatter\Query\Regular",
            'Property'      => "Congow\Orient\Formatter\Query\Regular",
            'Attribute'     => "Congow\Orient\Formatter\Query\Regular",
            'Value'         => "Congow\Orient\Formatter\Query\Regular",
        );
    }
}

