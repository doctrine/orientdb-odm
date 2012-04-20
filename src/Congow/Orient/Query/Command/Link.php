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
 * This class lets you create SQL statements in order to create links between
 * records.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command;

use Congow\Orient\Query\Command;

class Link extends Command
{
    /**
     * Sets the source of the link, its $alias and if the link must be $reverse.
     *
     * @param string  $class
     * @param string  $property
     * @param string  $alias
     * @param boolean $inverse
     */
    public function __construct($class, $property, $alias, $inverse = false)
    {
        parent::__construct();

        $this->setToken('SourceClass', $class);
        $this->setToken('SourceProperty', $property);
        $this->setToken('Name', $alias);

        if ($inverse) {
            $this->setToken('Inverse', 'INVERSE');
        }
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "CREATE LINK :Name FROM :SourceClass.:SourceProperty TO :DestinationClass.:DestinationProperty :Inverse";
    }

    /**
     * Sets the destination of the link.
     *
     * @param   string $class
     * @param   string $property
     * @return  Link
     */
    public function with($class, $property)
    {
        $this->setToken('DestinationClass', $class);
        $this->setToken('DestinationProperty', $property);

        return $this;
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Inverse'               => "Congow\Orient\Formatter\Query\Regular",
            'SourceClass'           => "Congow\Orient\Formatter\Query\Regular",
            'SourceProperty'        => "Congow\Orient\Formatter\Query\Regular",
            'DestinationClass'      => "Congow\Orient\Formatter\Query\Regular",
            'DestinationProperty'   => "Congow\Orient\Formatter\Query\Regular",
            'Name'                  => "Congow\Orient\Formatter\Query\Regular",
        ));
    }
}
