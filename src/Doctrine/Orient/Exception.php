<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This is a general Exception class for the Doctrine\Orient library.
 *
 * @package    Doctrine\Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient;

class Exception extends \Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}
