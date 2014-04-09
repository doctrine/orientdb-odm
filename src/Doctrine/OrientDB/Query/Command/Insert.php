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
 * Insert class, to build INSERT commands for Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

use Doctrine\OrientDB\LogicException;
use Doctrine\OrientDB\Query\Command;

class Insert extends Command implements InsertInterface
{
    /**
     * Sets the fields to insert within the query.
     *
     * @param  array   $fields
     * @param  boolean $append
     * @return Insert
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
        return "INSERT INTO :Target (:Fields) VALUES (:Values) :Returns";
    }

    /**
     * Sets the class in which the query will insert informations.
     *
     * @param  string $target
     * @return Insert
     */
    public function into($target)
    {
        $this->setToken('Target', $target);

        return $this;
    }

    /**
     * Sets the $values to insert.
     *
     * @param  array   $values
     * @param  boolean $append
     * @return Insert
     */
    public function values(array $values, $append = true)
    {
        $this->setTokenValues('Values', $values, $append);

        return $this;
    }

    /**
     * Sets the $returns type
     *
     * @param  string $return
     * @return Insert
     */
    public function returns($returns)
    {
        $returns = strtoupper($returns);
        if (!in_array($returns, $this->getValidReturnTypes())) {
            throw new LogicException(sprintf("Unknown return type %s", $returns));
        }
        $this->setToken('Returns', $returns);
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'Fields'  => "Doctrine\OrientDB\Query\Formatter\Query\Regular",
            'Values'  => "Doctrine\OrientDB\Query\Formatter\Query\Values",
            'Returns' => "Doctrine\OrientDB\Query\Formatter\Query\Returns"
        ));
    }

    /**
     * Returns the acceptable return types
     *
     * @return Array
     */
    public function getValidReturnTypes()
    {
        return array(
            self::RETURN_COUNT,
            self::RETURN_BEFORE,
            self::RETURN_AFTER
        );
    }

    /**
     * @inheritdoc
     */
    public function shouldReturn()
    {
        return true;
    }
}
