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

namespace Doctrine\OrientDB\Exception\Document;

use Doctrine\OrientDB\Exception;

class NotFound extends Exception
{
    const MESSAGE = 'The object can not be converted to a POPO mapped by the ODM.';

    public function __construct()
    {
        $this->message = self::MESSAGE;
    }
}
