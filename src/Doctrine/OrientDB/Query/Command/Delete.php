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
 * This class manages the creation of SQL statements able to delete records
 * in a class.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

use Doctrine\OrientDB\Query\Command;

class Delete extends Command
{
    /**
     * Builds a new statement, setting the class in which the records are gonna
     * be deleted.
     *
     * @param string $from
     */
    public function __construct($from)
    {
        parent::__construct();

        $this->setClass($from);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "DELETE FROM :Class :Returns :Where";
    }

    /**
     * Sets the query $class.
     *
     * @param string $class
     */
    protected function setClass($class)
    {
        $this->setToken('Class', $class);
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return Array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
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
            self::RETURN_BEFORE
        );
    }

    /**
     * @inheritdoc
     */
    public function canHydrate()
    {
        return self::RETURN_BEFORE === $this->getTokenValue('Returns');
    }
}
