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
 * An exception raised when an Json object cannot be converted to a POPO
 *
 * @package    Doctrine\OrientDB
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Exception\ODM\OClass;

use Doctrine\OrientDB\Exception;

class NotFound extends Exception
{
    const MESSAGE = 'Unable to find a PHP class mapped for "%s".';

    public function __construct($class)
    {
        $this->message = sprintf(self::MESSAGE, $class);
    }
}
