<?php

/**
 * SQL command Update interface
 *
 * @package    Doctrine\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Contract\Query\Command;

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
