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
 * Binding class
 *
 * This class is the foundation of the library : it's the low-level binding
 * connecting to Congow\Orient.
 * It's also responsible of incapsulating a proper client which makes HTTP
 * requests to Congow\OrientDB.
 *
 * @package    Congow\Orient
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Foundation;

use Congow\Orient\Contract\Protocol;
use Congow\Orient\Contract\Http;
use Congow\Orient\Exception;

class Binding implements Protocol\Http
{
    protected $server;
    protected $client;
    protected $username;
    protected $password;
    protected $authentication;

    /**
     * Instantiates a new binding.
     *
     * @api
     * @param Http\Client $client
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct(Http\Client $client, $host = '127.0.0.1', $port = 2480, $username = null, $password = null, $database = null)
    {
        $this->client = $client;
        $this->server = $host . ($port ? sprintf(':%s', $port) : false);
        $this->database = $database;
        $this->setAuthentication($username, $password);
    }

    /**
     * Creates a relative URL for the specified OrientDB method call.
     *
     * @param   string $method
     * @param   string $database
     * @param   array  $arguments
     * @return  string
     */
    protected function getLocation($method, $database = null, array $arguments = null)
    {
        $location = "http://{$this->server}/$method";

        if ($database) {
            $location .= '/' . rawurlencode($database);
        }
        if ($arguments) {
            $location .= '/' . implode('/', array_map('rawurlencode', $arguments));
        }

        return $location;
    }

    /**
     * Returns the URL for the execution of a SQL query.
     *
     * @param   string $sql
     * @param   int    $limit
     * @param   string $fetchPlan
     * @return  string
     */
    protected function getQueryLocation($sql, $limit = null, $fetchPlan = null)
    {
        $arguments = array('sql', $sql);

        if (isset($limit)) {
            $arguments[] = $limit;
        }
        if (isset($fetchPlan)) {
            $arguments[] = $fetchPlan;
        }

        return $this->getLocation('query', $this->database, $arguments);
    }

    /**
     * Returns the URL to fetch a document.
     *
     * @param   string $class
     * @return  string
     */
    protected function getDocumentLocation($rid = null, $fetchPlan = null)
    {
        $arguments = array($rid);

        if ($fetchPlan) {
            $arguments[] = $fetchPlan;
        }

        return $this->getLocation('document', $this->database, $arguments);
    }

    /**
     * Returns the URL to fetch a class.
     *
     * @param   string $class
     * @return  string
     */
    protected function getClassLocation($class)
    {
        return $this->getLocation('class', $this->database, array($class));
    }

    /**
     * Returns the URL to fetch a cluster.
     *
     * @param   string $cluster
     * @param   int    $limit
     * @return  string
     */
    protected function getClusterLocation($cluster, $limit = null)
    {
        return $this->getLocation('cluster', $this->database, array($cluster, $limit));
    }

    /**
     * Returns the URL to fetch a database.
     *
     * @param   string $database
     * @return  string
     */
    protected function getDatabaseLocation($database)
    {
        return $this->getLocation('database', $database);
    }
    /**
     * Deletes a class.
     *
     * @api
     * @param   string $class
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function deleteClass($class, $database = false)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->getHttpClient()->delete($location);

        return $response;
    }

    /**
     * Gets a class and its records.
     *
     * @api
     * @param   string $class
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function getClass($class, $database = false)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Creates a new class.
     *
     * @api
     * @param   string $class
     * @param   string $database
     * @param   string $body
     * @return  Congow\Orient\Http\Response
     */
    public function postClass($class, $database = false, $body = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->getHttpClient()->post($location, $body);

        return $response;
    }

    /**
     * Gets informations about a cluster.
     *
     * @api
     * @param   string $cluster
     * @param   boolean $database
     * @return  Congow\Orient\Http\Response
     */
    public function cluster($cluster, $database = false, $limit = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getClusterLocation($cluster, $limit);
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Connects the instance to a DB.
     *
     * @api
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function connect($database)
    {
        $location = $this->getDatabaseLocation($database);
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Disconnect this instance from the server.
     *
     * @api
     * @return Congow\Orient\Http\Response
     */
    public function disconnect()
    {
        $location = $this->getLocation('disconnect');
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Gets the current server
     *
     * @api
     * @return Congow\Orient\Http\Response
     */
    public function getServer()
    {
        $location = $this->getLocation('server');
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Gets informations about a DB.
     *
     * @api
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function getDatabase($database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDatabaseLocation($this->database);
        $response = $this->getHttpclient()->get($location);

        return $response;
    }

    /**
     * Executes a raw SQL query on the given DB.
     *
     * @api
     * @param   string $sql
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function command($sql, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getLocation('command', $this->database, array('sql', $sql));
        $response = $this->getHttpClient()->post($location, null);

        return $response;
    }

    /**
     * Executes a raw SQL query.
     *
     * It differs from the command because Congow\Orient defines a query as a SELECT only.
     *
     * @api
     * @param   string $sql           The query
     * @param   string $database
     * @param   int $limit            Results limit, default 20
     * @param   string $fetchPlan
     * @return  Congow\Orient\Http\Response
     */
    public function query($sql, $database = null, $limit = null, $fetchPlan = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getQueryLocation($sql, $limit, $fetchPlan);
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Retrieves a record.
     *
     * @api
     * @param   string $rid
     * @param   string $database
     * @param   string $fetchPlan
     * @return  Congow\Orient\Http\Response
     */
    public function getDocument($rid, $database = null, $fetchPlan = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation($rid, $fetchPlan);
        $response = $this->getHttpClient()->get($location);

        return $response;
    }

    /**
     * Creates a new record.
     *
     * @api
     * @param   string $document
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function postDocument($document, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation();
        $response = $this->getHttpClient()->post($location, $document);

        return $response;
    }

    /**
     * Updates an existing record.
     *
     * @api
     * @param   string $rid
     * @param   string $document
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function putDocument($rid, $document, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation($rid);
        $response = $this->getHttpClient()->put($location, $document);

        return $response;
    }

    /**
     * Deletes a document.
     *
     * @api
     * @param   string $rid
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function deleteDocument($rid, $version = null, $database = null)
    {
        $this->resolveDatabase($database);

        if ($version) {
            $this->getHttpClient()->setHeader('If-Match', $version);
        }

        $location = $this->getDocumentLocation($rid);
        $response = $this->getHttpClient()->delete($location);

        return $response;
    }

    /**
     * Sets the database for the current instance.
     *
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Checks wheter the current object is able to perform a request on a DB.
     *
     * @return true
     * @throws Exception
     */
    protected function checkDatabase()
    {
        if (!is_null($this->database)) {
            return true;
        }

        throw new Exception(sprintf('In order to perform the operation you must specify a database'));
    }

    /**
     * Assigns a database to the current instance.
     *
     * @param string $database
     */
    protected function resolveDatabase($database = false)
    {
        $this->database = $database ? : $this->database;
        $this->checkDatabase();
    }

    /**
     * Gets the authentication token.
     *
     * @return string
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * Assigns the authentication string if, at least, one among username or
     * password are valid.
     * The authentication attribute is in HTTP header style.
     *
     * @param   string $username
     * @param   string $password
     * @return  bool
     */
    public function setAuthentication($username = null, $password = null)
    {
        $this->username = $username ? : $this->username;
        $this->password = $password ? : $this->password;

        $this->authentication = sprintf('%s:%s', $this->username, $this->password);

        if ($this->authentication === ':') {
            $this->authentication = false;
        }

        return $this->getHttpclient()->setAuthentication($this->authentication);
    }

    /**
     * Injects the HttpClient instance inside the binding.
     *
     * @param Http\Client $client
     */
    public function setHttpClient(Http\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the Httpclient of the binding.
     *
     * @return Http\Client
     */
    public function getHttpClient()
    {
        return $this->client;
    }
}
