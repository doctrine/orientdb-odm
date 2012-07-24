<?php

/**
 * SQL command Update interface
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Query\Command;

interface Update
{
    /**
     * Set the $values of the updates to be done.
     * You can $appnd the values.
     *
     * @param   array   $values
     * @param   boolean $append
     * @return  Update
     */
    public function set(array $values, $append);
}
