<?php

/*
 * This file is part of the Orient package.
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
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Update;

use Orient\Query\Command\Update;

class Add extends Update
{
    const SCHEMA =
        "UPDATE :Class ADD :RidUpdates :Where"
    ;

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
     * Returns the formatters for this query's tokens.
     *
     * @return array
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
            'RidUpdates'  => "Orient\Formatter\Query\RidUpdates",
        ));
    }
}
