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
 * Class Overflow
 *
 * @package     Congow\Orient
 * @subpackage  Exception
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Exception;

use Congow\Orient\Exception;

class Overflow extends Exception
{
    public function __construct($message)
    {
        $this->message = $message;
    }
}

