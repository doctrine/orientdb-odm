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
 * An exception raised when an Json object cannot be converted to a POPO
 *
 * @package    Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Exception\ODM\OClass;

use Orient\Exception;

class NotFound extends Exception
{
    const MESSAGE = 'Unable to find a PHP class mapped for "%s".';

    public function __construct($class)
    {
        $this->message = sprintf(self::MESSAGE, $class);
    }
}
