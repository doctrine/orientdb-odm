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
 * Command used to alter properties in a class.
 *
 * @package
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Property;

use Doctrine\OrientDB\Query\Command\Property;

class Alter extends Property
{
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
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "ALTER PROPERTY :Class.:Property :Attribute :Value";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array(
            'Class'         => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Property'      => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Attribute'     => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Value'         => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
        );
    }
}

