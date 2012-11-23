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

use Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface;
use Doctrine\OrientDB\Binding\Adapter\CurlClientAdapter;
use Doctrine\OrientDB\Binding\Client\Http\CurlClient;
use Doctrine\OrientDB\Exception as OrientException;

class HttpBinding implements HttpBindingInterface
{
    protected $server;
    protected $database;
    protected $adapter;

    /**
     * Instantiates a new binding.
     *
     * @param BindingParameters $parameters
     * @param HttpClientAdapterInterface $adapter
     */
    public function __construct(BindingParameters $parameters, HttpClientAdapterInterface $adapter = null)
    {
        $this->server = "{$parameters->getHost()}:{$parameters->getPort()}";
        $this->database = $parameters->getDatabase();
        $this->adapter = $adapter ?: new CurlClientAdapter(new CurlClient());

        $this->setAuthentication($parameters->getUsername(), $parameters->getPassword());
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
     * @param string $database
     * @param string $sql
     * @param int $limit
     * @param string $fetchPlan
     * @return string
     */
    protected function getQueryLocation($database, $sql, $limit = null, $fetchPlan = null)
    {
        $arguments = array('sql', $sql);

        if (isset($limit)) {
            $arguments[] = $limit;
        }

        if (isset($fetchPlan)) {
            $arguments[] = $fetchPlan;
        }

        $location = $this->getLocation('query', $database, $arguments);

        return $location;
    }

    /**
     * Returns the URL to fetch a document.
     *
     * @param string $database
     * @param string $rid
     * @param string $fetchPlan
     * @return string
     */
    protected function getDocumentLocation($database, $rid = null, $fetchPlan = null)
    {
        $this->ensureDatabase($database);
        $arguments = array($rid);

        if ($fetchPlan) {
            $arguments[] = $fetchPlan;
        }

        $location = $this->getLocation('document', $database, $arguments);

        return $location;
    }

    /**
     * Returns the URL to fetch a class.
     *
     * @param string $database
     * @param string $class
     * @return string
     */
    protected function getClassLocation($database, $class)
    {
        $this->ensureDatabase($database);
        $location = $this->getLocation('class', $database, array($class));

        return $location;
    }

    /**
     * Returns the URL to fetch a cluster.
     *
     * @param string $database
     * @param string $cluster
     * @param int $limit
     * @return string
     */
    protected function getClusterLocation($database, $cluster, $limit = null)
    {
        $this->ensureDatabase($database);
        $location = $this->getLocation('cluster', $database, array($cluster, $limit));

        return $location;
    }

    /**
     * Returns the URL to fetch a database.
     *
     * @param string $database
     * @return string
     */
    protected function getDatabaseLocation($database)
    {
        $this->ensureDatabase($database);
        $location = $this->getLocation('database', $database);

        return $location;
    }
    /**
     * Deletes a class.
     *
     * @api
     * @param string $class
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteClass($class, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);
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
    public function getClass($class, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * Creates a new class.
     *
     * @api
     * @param string $class
     * @param string $body
     * @param string $database
     * @return BindingResultInterface
     */
    public function postClass($class, $body = null, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);
        $response = $this->adapter->request('POST', $location, null, $body);

        return $response;
    }

    /**
     * Gets informations about a cluster.
     *
     * @api
     * @param string $cluster
     * @param string $database
     * @return BindingResultInterface
     */
    public function cluster($cluster, $limit = null, $database = null)
    {
        $location = $this->getClusterLocation($database ?: $this->database, $cluster, $limit);
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
        $location = $this->getDatabaseLocation($database ?: $this->database);
        $response = $this->adapter->request('GET', $location);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($sql, $return = false)
    {
        if (is_string($return)) {
            return $this->query($sql, -1, $return);
        }

        if ($return == true) {
            return $this->query($sql, -1);
        }

        $response = $this->command($sql, $this->database);

        return $response;
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
        $database = $database ?: $this->database;
        $this->ensureDatabase($database);

        $location = $this->getLocation('command', $database, array('sql', $sql));
        $response = $this->adapter->request('POST', $location);

        return $response;
    }

    /**
     * Executes a raw SQL query.
     *
     * It differs from the command because Doctrine\OrientDB defines a query as a SELECT only.
     *
     * @api
     * @param string $sql SQL query.
     * @param int $limit Maximum number of records (default is 20).
     * @param string $fetchPlan
     * @param string $database
     * @return BindingResultInterface
     */
    public function query($sql, $limit = null, $fetchPlan = null, $database = null)
    {
        $location = $this->getQueryLocation($database ?: $this->database, $sql, $limit, $fetchPlan);
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
        $location = $this->getDocumentLocation($database ?: $this->database, $rid, $fetchPlan);
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
        $location = $this->getDocumentLocation($database ?: $this->database);
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
        $location = $this->getDocumentLocation($database ?: $this->database, $rid);
        $response = $this->adapter->request('PUT', $location, null, $document);

        return $response;
    }

    /**
     * Deletes the document identified by the given $rid in the $database.
     *
     * @api
     * @param string $rid
     * @param string $version
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteDocument($rid, $version = null, $database = null)
    {
        $headers = null;

        if ($version) {
            $headers = array('If-Match' => $version);
        }

        $location = $this->getDocumentLocation($database ?: $this->database, $rid);
        $response = $this->adapter->request('DELETE', $location, $headers);

        return $response;
    }

    /**
     * Sets the default database for the current binding instance.
     *
     * @param string $database
     */
    public function setDatabase($database)
    {
        $this->ensureDatabase($database);
        $this->database = $database;
    }

    /**
     * Checks wheter the specified database string is valid to perform a request.
     *
     * @throws OrientException
     */
    protected function ensureDatabase($database)
    {
        if (strlen($database) === 0) {
            throw new OrientException('In order to perform the operation you must specify a database');
        }
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
