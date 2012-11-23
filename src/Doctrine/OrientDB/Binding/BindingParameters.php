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
 * Standard HTTP binding class used by Orient.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding;

use InvalidArgumentException;

class BindingParameters
{
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT = 2480;

    private $host;
    private $port;
    private $username;
    private $password;
    private $database;

    /**
     * Creates a new binding parameters instance.
     *
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host = null, $port = null, $username = null, $password = null, $database = null)
    {
        $this->host = $host ?: self::DEFAULT_HOST;
        $this->port = $port ?: self::DEFAULT_PORT;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * Creates a new binding parameters instance from a URI string
     * or a parameters array.
     *
     * @param mixed $parameters
     * @return BindingParameters
     */
    public static function create($parameters)
    {
        if (is_string($parameters)) {
            return self::fromString($parameters);
        }

        if (is_array($parameters)) {
            return self::fromArray($parameters);
        }

        throw new InvalidArgumentException('Invalid parameters type');
    }

    /**
     * Creates a new binding parameters instance from a URI string.
     *
     * @param string $parameters
     * @return BindingParameters
     */
    public static function fromString($uri)
    {
        $parameters = parse_url($uri);

        if (isset($parameters['user'])) {
            $parameters['username'] = $parameters['user'];
        }

        if (isset($parameters['pass'])) {
            $parameters['password'] = $parameters['pass'];
        }

        if (isset($parameters['path']) && strlen($parameters['path']) > 0) {
            list(, $parameters['database']) = explode('/', $parameters['path'], 2);
        }

        unset($parameters['path'], $parameters['user'], $parameters['pass']);

        return self::fromArray($parameters);
    }

    /**
     * Creates a new binding parameters instance from a parameters array.
     *
     * @param array $parameters
     * @return BindingParameters
     */
    public static function fromArray(Array $parameters)
    {
        $host = isset($parameters['host']) ? $parameters['host'] : self::DEFAULT_HOST;
        $port = isset($parameters['port']) ? $parameters['port'] : self::DEFAULT_PORT;
        $user = isset($parameters['username']) ? $parameters['username'] : null;
        $pass = isset($parameters['password']) ? $parameters['password'] : null;
        $db = isset($parameters['database']) ? $parameters['database'] : null;

        return new self($host, $port, $user, $pass, $db);
    }

    /**
     * Returns the specified IP host pointing to the OrientDB server.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the specified TCP port pointing to the OrientDB server.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns the specified username to access the OrientDB server.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the specified password to access the OrientDB server.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the specified database on the OrientDB server.
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }
}
