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
 * An exception raised when an Json object cannot be converted to a POPO
 *
 * @package    Congow\Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Exception\Document;

use Congow\Orient\Exception;

class NotFound extends Exception
{
    const MESSAGE = 'The object can not be converted to a POPO mapped by the ODM.';

    public function __construct()
    {
        $this->message = self::MESSAGE;
    }
}
