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
 * OClass interface manages the construction of class-related SQL commands.
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Query\Command;

interface OClass
{
    /**
     * Sets the class to manipulate within this command.
     *
     * @param   string  $class
     */
    public function __construct($class);
}
