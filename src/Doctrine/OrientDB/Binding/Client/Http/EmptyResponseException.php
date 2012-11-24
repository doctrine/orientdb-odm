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
 * An exception raised when an HTTP response is empty.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding\Client\Http;

use Doctrine\OrientDB\Exception;

class EmptyResponseException extends Exception
{
    /**
     * Generates an exception giving information about the client which performed
     * the request and the unreachable location.
     *
     * @param string $client
     * @param string $location
     */
    public function __construct($client, $location)
    {
        $this->client = $client;
        $this->location = $location;

        $clientClass = get_class($client);

        parent::__construct("$clientClass has been unable to retrieve a response for the resource at $location");
    }

    /**
     * Returns the client instance that generated the exception.
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the URL that generated the exception.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}
