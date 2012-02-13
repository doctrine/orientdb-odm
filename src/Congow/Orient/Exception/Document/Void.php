<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * (c) David Funaro <ing.davidino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An exception raised when an Object has no setted values
 *
 * @package    Congow\Orient
 * @subpackage Exception
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\Exception\Document;

use Congow\Orient\Exception;

class Void extends Exception
{
    const MESSAGE = 'Objects persisted need to have at least one non-empty fields';

    public function __construct()
    {
        $this->message = self::MESSAGE;
    }
}