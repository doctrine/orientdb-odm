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
 * This is a general Exception class for the Doctrine\OrientDB library.
 *
 * @package    Doctrine\OrientDB
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB;

class Exception extends \Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}
