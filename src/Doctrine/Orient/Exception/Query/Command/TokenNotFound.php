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
 * TokenNotFound exceptionclass gets raised when you try to replace a token, in
 * a command, which isn't part of the command schema.
 *
 * For example, when you add a LIMIT clause on a DELETE.
 *
 * @package    Doctrine\Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Exception\Query\Command;

use Doctrine\Orient\Exception;

class TokenNotFound extends Exception
{
    const MESSAGE =
        "The token %s is not contained in the %s command schema\n
        The command schema is: %s";

    /**
     * Compares the class SCHEMA and the submitted token.
     *
     * @param string $token
     * @param string $commandClass
     */
    public function __construct($token, $commandClass)
    {
        $ref = new \ReflectionClass($commandClass);
        $schema = $ref->getConstant('SCHEMA') ? : "undefined";

        $this->message = sprintf(self::MESSAGE, $token, $commandClass, $schema);
    }
}
