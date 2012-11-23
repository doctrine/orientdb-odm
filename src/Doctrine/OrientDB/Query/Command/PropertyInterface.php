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
 * Property interface is responsible to set the property (belonging to a class)
 * to manipulate.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

interface PropertyInterface
{
    /**
     * Sets the class of the property to manipulate.
     *
     * @param   string  $class
     * @return  Property
     */
    public function on($class);
}
