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
 * Command used in order to remove values from a class.
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Update;

use Congow\Orient\Query\Command\Update;

class Remove extends Update
{
    /**
     * Builds a new statement setting the $values to remove in the given $class.
     * The values to remove can be appended with the $append parameter.
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
        return "UPDATE :Class REMOVE :RidUpdates :Where";
    }

    /**
     * Returns the formatters for this query tokens
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
