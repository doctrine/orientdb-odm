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
     * @param String $host
     * @param String $port
     * @param String $username
     * @param String $password
     */
    public function __construct(Http\Client $client, $host = '127.0.0.1', $port = 2480, $username = null, $password = null, $database = null)
    {
        $this->server = $host . ($port ? sprintf(':%s', $port) : false);
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->client = $client;

        $this->setAuthentication($username, $password);
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

        return $this->getHttpClient()->delete($location);
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

        return $this->getHttpClient()->get($location);
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

        return $this->getHttpClient()->post($location, $body);
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

        return $this->getHttpClient()->get($location);
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

        return $this->getHttpClient()->get($location);
    }

    /**
     * Disconnect this instance from the server.
     *
     * @api
     * @return Congow\Orient\Http\Response
     */
    public function disconnect()
    {
        return $this->getHttpClient()->get($this->server . '/disconnect');
    }

    /**
     * Gets the current server
     *
     * @api
     * @return Congow\Orient\Http\Response
     */
    public function getServer()
    {
        return $this->getHttpClient()->get($this->server . '/server');
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
        $location = $this->server . '/command/' . $this->database . '/sql/' . urlencode($sql);

        return $this->getHttpClient()->post($location, null);
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

        return $this->getHttpclient()->get($location);
    }

    /**
     * Executes a raw query. It differs from the command because Congow\Orient defines
     * a query as a SELECT only.
     *
     * @api
     * @param   string $sql           The query
     * @param   string $database
     * @param   Int $limit            Results limit, default 20
     * @param   string $fetchPlan
     * @return  Congow\Orient\Http\Response
     */
    public function query($sql, $database = null, $limit = null, $fetchPlan = null)
    {
        $this->resolveDatabase($database);
        $location = $this->server . '/query/' . $this->database . '/sql/' . urlencode($sql);

        if ($limit) {
            $location .= '/' . (int) $limit;

            $location = $this->addFetchPlan($fetchPlan, $location);
        }

        return $this->getHttpClient()->get($location);
    }

    /**
     * Retrieves a record
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
        $location = $this->server . '/document/' . $this->database . '/' . $rid;
        $location = $this->addFetchPlan($fetchPlan, $location);

        return $this->getHttpClient()->get($location);
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
        $location = $this->server . '/document/' . $this->database;

        return $this->getHttpClient()->post($location, $document);
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
        $location = $this->server . '/document/' . $this->database . '/' . $rid;

        return $this->getHttpClient()->put($location, $document);
    }

    /**
     * Deletes a document
     *
     * @api
     * @param   string $rid
     * @param   string $database
     * @return  Congow\Orient\Http\Response
     */
    public function deleteDocument($rid, $version = null, $database = null)
    {
        $this->resolveDatabase($database);
        $location = $this->server . '/document/' . $this->database . '/' . $rid;
        
        if ($version) {
          $this->getHttpClient()->setHeader('If-Match', $version);
        }

        return $this->getHttpClient()->delete($location);
    }

    /**
     * Assigns a database to the current instance.
     *
     * @param String $database
     */
    protected function resolveDatabase($database = false)
    {
        $this->database = $database ? : $this->database;
        $this->checkDatabase();
    }

    /**
     * Gets the authentication token.
     *
     * @return String
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
     * Sets the database for the current instance.
     *
     * @param String $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * Injects the HttpClient instance inside the binding.
     *
     * @param Contract\Httpclient $client
     */
    public function setHttpClient(Http\Client $client)
    {
        $this->client = $client;
    }

    /**
     * Returns the Httpclient of the binding.
     *
     * @return Contract\Httpclient
     */
    public function getHttpClient()
    {
        return $this->client;
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
     * Appends the fetchPlan to the location.
     *
     * @param   string $fetchPlan
     * @param   string $location
     * @return  String
     */
    protected function addFetchPlan($fetchPlan, $location)
    {
        return $location .= '/' . $fetchPlan;
    }

    /**
     * Returns the location of a Class.
     *
     * @param   string $class
     * @return  String
     */
    final protected function getClassLocation($class)
    {
        return $this->server . '/class/' . $this->database . '/' . $class;
    }

    /**
     * Returns the location of a Cluster.
     *
     * @param   string  $cluster
     * @param   Integer $limit
     * @return  String
     */
    final protected function getClusterLocation($cluster, $limit = null)
    {
        return $this->server . '/cluster/' . $this->database . '/' . $cluster . ($limit ? '/' . $limit : '');
    }

    /**
     * Returns the location of a Database.
     *
     * @param   string $database
     * @return  String
     */
    final protected function getDatabaseLocation($database)
    {
        return $this->server . '/database/' . $database;
    }
}
