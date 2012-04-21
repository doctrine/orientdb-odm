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
 * Insert class, to build INSERT commands for Congow\OrientDB.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command;

use Congow\Orient\Contract\Query\Command\Insert as InsertInterface;
use Congow\Orient\Query\Command;

class Insert extends Command implements InsertInterface
{
    /**
     * Sets the fields to insert within the query.
     *
     * @param   array   $fields
     * @param   boolean $append
     * @return  Insert
     */
    public function fields(array $fields, $append = true)
    {
        $this->setTokenValues('Fields', $fields, $append);

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "INSERT INTO :Target (:Fields) VALUES (:Values)";
    }

    /**
     * Sets the class in which the query will insert informations.
     *
     * @param   string $target
     * @return  Insert
     */
    public function into($target)
    {
        $this->setToken('Target', $target);

        return $this;
    }

    /**
     * Sets the $values to insert.
     *
     * @param   array   $values
     * @param   boolean $append
     * @return  Insert
     */
    public function values(array $values, $append = true)
    {
        $this->setTokenValues('Values', $values, $append);

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
            'Fields' => "Congow\Orient\Formatter\Query\Regular",
            'Values' => "Congow\Orient\Formatter\Query\Values",
        ));
    }
}
