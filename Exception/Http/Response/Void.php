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
 * An exception raised when an HTTP response is empty.
 *
 * @package    Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Exception\Http\Response;

use \Orient\Exception;

class Void extends Exception
{
    const MESSAGE = 'The %s client has been unable to retrieve a response for the resource at %s';

    /**
     * Generates an exception giving information about the client which performed
     * the request and the unreachable location.
     *
     * @param string $client
     * @param string $location
     */
    public function __construct($client, $location)
    {
        $this->message = sprintf(self::MESSAGE, $client, $location);
    }
}
