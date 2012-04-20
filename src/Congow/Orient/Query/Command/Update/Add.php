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
 * This class manages the creation of a SQL statement able to add a relation in
 * a record's attribute.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Update;

use Congow\Orient\Query\Command\Update;

class Add extends Update
{
    /**
     * Builds a new statement setting the $values in the given $class.
     * You can $append the values.
     *
     * @param array   $values
     * @param string  $class
     * @param boolean $append
     */
    public function __construct(array $values, $class, $append = true)
    {
        parent::__construct($class);

        $this->setTokenValues('RidUpdates', $values, $append);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "UPDATE :Class ADD :RidUpdates :Where";
    }

    /**
     * Returns the formatters for this query's tokens.
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'RidUpdates'  => "Congow\Orient\Formatter\Query\RidUpdates",
        ));
    }
}
