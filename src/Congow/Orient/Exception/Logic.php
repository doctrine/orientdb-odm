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
 * Class Logic
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Congow\Orient\Exception;

use Congow\Orient\Exception;

class Logic extends Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}

