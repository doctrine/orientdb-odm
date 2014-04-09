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
 * This is a central point to manipulate SQL statements dealing with updates.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

use Doctrine\OrientDB\Query\Command;

class Update extends Command implements UpdateInterface
{
    /**
     * Builds a new statement, setting the $class.
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct();

        $this->setToken('Class', $class);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "UPDATE :Class SET :Updates :Where :Returns";
    }

    /**
     * Set the $values of the updates to be done.
     * You can $appnd the values.
     *
     * @param  array   $values
     * @param  boolean $append
     * @return Update
     */
    public function set(array $values, $append = true)
    {
        $this->setTokenValues('Updates', $values, $append);

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
            'Updates' => "Doctrine\OrientDB\Query\Formatter\Query\Updates",
            'Returns' => "Doctrine\OrientDB\Query\Formatter\Query\Returns"
        ));
    }

    /**
     * Sets the $returns type
     *
     * @param  string $return
     * @return Update
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
