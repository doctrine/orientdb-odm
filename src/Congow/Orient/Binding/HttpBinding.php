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
 * Standard HTTP binding class used by Orient.
 *
 * @package    Congow\Orient
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Congow\Orient\Binding;

use Congow\Orient\Contract\Binding\BindingInterface;
use Congow\Orient\Contract\Binding\Adapter\HttpClientAdapterInterface;
use Congow\Orient\Client\Http\CurlClient;
use Congow\Orient\Binding\Adapter\CurlClientAdapter;
use Congow\Orient\Exception as OrientException;

class HttpBinding implements BindingInterface
{
    protected $adapter;
    protected $server;

    /**
     * Instantiates a new binding.
     *
     * @api
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host = '127.0.0.1', $port = 2480, $username = null, $password = null, $database = null)
    {
        $this->server = $host . ($port ? sprintf(':%s', $port) : false);
        $this->database = $database;

        $client = new CurlClient();

        $this->adapter = new CurlClientAdapter($client);
        $this->adapter->setAuthentication($username, $password);
    }

    /**
     * Creates a relative URL for the specified OrientDB method call.
     *
     * @param string $method
     * @param string $database
     * @param array $arguments
     * @return string
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
     * @param string $sql
     * @param int $limit
     * @param string $fetchPlan
     * @return string
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
     * @param string $rid
     * @param string $fetchPlan
     * @return string
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
     * @param string $class
     * @return string
     */
    protected function getClassLocation($class)
    {
        return $this->getLocation('class', $this->database, array($class));
    }

    /**
     * Returns the URL to fetch a cluster.
     *
     * @param string $cluster
     * @param int $limit
     * @return string
     */
    protected function getClusterLocation($cluster, $limit = null)
    {
        return $this->getLocation('cluster', $this->database, array($cluster, $limit));
    }

    /**
     * Returns the URL to fetch a database.
     *
     * @param string $database
     * @return string
     */
    protected function getDatabaseLocation($database)
    {
        return $this->getLocation('database', $database);
    }
    /**
     * Deletes a class.
     *
     * @api
     * @param string $class
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteClass($class, $database = false)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->adapter->request('DELETE', $location);

        return $response;
    }

    /**
     * Gets a class and its records.
     *
     * @api
     * @param string $class
     * @param string $database
     * @return BindingResultInterface
     */
    public function getClass($class, $database = false)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Creates a new class.
     *
     * @api
     * @param string $class
     * @param string $database
     * @param string $body
     * @return BindingResultInterface
     */
    public function postClass($class, $database = false, $body = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getClassLocation($class);
        $response = $this->adapter->request('POST', $location, null, $body);

        return $response;
    }

    /**
     * Gets informations about a cluster.
     *
     * @api
     * @param string $cluster
     * @param boolean $database
     * @return BindingResultInterface
     */
    public function cluster($cluster, $database = false, $limit = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getClusterLocation($cluster, $limit);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Connects the instance to a DB.
     *
     * @api
     * @param string $database
     * @return BindingResultInterface
     */
    public function connect($database)
    {
        $location = $this->getDatabaseLocation($database);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Disconnect this instance from the server.
     *
     * @api
     * @return BindingResultInterface
     */
    public function disconnect()
    {
        $location = $this->getLocation('disconnect');
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Gets the current server.
     *
     * @api
     * @return BindingResultInterface
     */
    public function getServer()
    {
        $location = $this->getLocation('server');
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Gets informations about a DB.
     *
     * @api
     * @param string $database
     * @return BindingResultInterface
     */
    public function getDatabase($database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDatabaseLocation($this->database);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql, $return = false)
    {
        if (is_string($return)) {
            return $this->query($sql, null, -1, $return);
        }

        if ($return == true) {
            return $this->query($sql, null, -1);
        }

        return $this->command($sql, $this->database);
    }

    /**
     * Executes a raw SQL query on the given DB.
     *
     * @api
     * @param string $sql
     * @param string $database
     * @return BindingResultInterface
     */
    public function command($sql, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getLocation('command', $this->database, array('sql', $sql));
        $response = $this->adapter->request('POST', $location);

        return $response;
    }

    /**
     * Executes a raw SQL query.
     *
     * It differs from the command because Congow\Orient defines a query as a SELECT only.
     *
     * @api
     * @param string $sql SQL query.
     * @param string $database
     * @param int $limit Maximum number of records (default is 20).
     * @param string $fetchPlan
     * @return BindingResultInterface
     */
    public function query($sql, $database = null, $limit = null, $fetchPlan = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getQueryLocation($sql, $limit, $fetchPlan);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Retrieves a record.
     *
     * @api
     * @param string $rid
     * @param string $database
     * @param string $fetchPlan
     * @return BindingResultInterface
     */
    public function getDocument($rid, $database = null, $fetchPlan = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation($rid, $fetchPlan);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Creates a new record.
     *
     * @api
     * @param string $document
     * @param string $database
     * @return BindingResultInterface
     */
    public function postDocument($document, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation();
        $response = $this->adapter->request('POST', $location, null, $document);

        return $response;
    }

    /**
     * Updates an existing record.
     *
     * @api
     * @param string $rid
     * @param string $document
     * @param string $database
     * @return BindingResultInterface
     */
    public function putDocument($rid, $document, $database = null)
    {
        $this->resolveDatabase($database);

        $location = $this->getDocumentLocation($rid);
        $response = $this->adapter->request('PUT', $location, null, $document);

        return $response;
    }

    /**
     * Deletes a document.
     *
     * @api
     * @param string $rid
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteDocument($rid, $version = null, $database = null)
    {
        $headers = null;

        $this->resolveDatabase($database);

        if ($version) {
            $headers = array('If-Match' => $version);
        }

        $location = $this->getDocumentLocation($rid);
        $response = $this->adapter->request('DELETE', $location, $headers);

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
     * @throws OrientException
     */
    protected function checkDatabase()
    {
        if (!is_null($this->database)) {
            return true;
        }

        throw new OrientException('In order to perform the operation you must specify a database');
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
     * Sets the username and password used to authenticate to the server.
     *
     * @param string $username
     * @param string $password
     */
    public function setAuthentication($username = null, $password = null)
    {
        $this->adapter->setAuthentication($username, $password);
    }

    /**
     * Sets the underlying HTTP client adapter.
     *
     * @param HttpClientAdapterInterface $client
     */
    public function setAdapter(HttpClientAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Sets the underlying HTTP client adapter.
     *
     * @return HttpClientAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
