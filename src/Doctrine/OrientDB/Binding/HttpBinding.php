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
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Query\Command\Select;
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
     * Returns the URL for the execution of a query.
     *
     * @param string $database
     * @param string $query
     * @param int $limit
     * @param string $fetchPlan
     * @return string
     */
    protected function getQueryLocation($database, $query, $limit = null, $fetchPlan = null, $language = BindingInterface::LANGUAGE_SQLPLUS)
    {
        $arguments = array($language, $query);

        if (isset($limit)) {
            $arguments[] = $limit;
        }

        if (isset($fetchPlan)) {
            $arguments[] = $fetchPlan;
        }

        return $this->getLocation('query', $database, $arguments);
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

        return $this->getLocation('document', $database, $arguments);
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

        return $this->getLocation('class', $database, array($class));
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

        return $this->getLocation('cluster', $database, array($cluster, $limit));
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

        return $this->getLocation('database', $database);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteClass($class, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);

        return $this->adapter->request('DELETE', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass($class, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function postClass($class, $body = null, $database = null)
    {
        $location = $this->getClassLocation($database ?: $this->database, $class);

        return $this->adapter->request('POST', $location, null, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function cluster($cluster, $limit = null, $database = null)
    {
        $location = $this->getClusterLocation($database ?: $this->database, $cluster, $limit);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function connect($database)
    {
        $location = $this->getDatabaseLocation($database);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        $location = $this->getLocation('disconnect');

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function getServer()
    {
        $location = $this->getLocation('server');

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabase($database = null)
    {
        $location = $this->getDatabaseLocation($database ?: $this->database);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function createDatabase($database, $storage = 'memory', $type = 'document')
    {
        $location = $this->getLocation('database', $database, array($storage, $type));

        return $this->adapter->request('POST', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function listDatabases()
    {
        $location = $this->getLocation('listDatabases');

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDatabase($database)
    {
        $location = $this->getLocation('database', $database);

        return $this->adapter->request('DELETE', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Query $query, $fetchPlan = null)
    {
        if ($query->getCommand() instanceof Select) {
            return $this->query($query->getRaw(), -1, $fetchPlan);
        }

        return $this->command($query->getRaw());
    }

    /**
     * {@inheritdoc}
     */
    public function command($query, $language = BindingInterface::LANGUAGE_SQLPLUS, $database = null)
    {
        $database = $database ?: $this->database;
        $this->ensureDatabase($database);

        $location = $this->getLocation('command', $database, array($language, $query));

        return $this->adapter->request('POST', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, $limit = null, $fetchPlan = null, $language = BindingInterface::LANGUAGE_SQLPLUS, $database = null)
    {
        $location = $this->getQueryLocation($database ?: $this->database, $query, $limit, $fetchPlan, $language);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocument($rid, $database = null, $fetchPlan = null)
    {
        $location = $this->getDocumentLocation($database ?: $this->database, $rid, $fetchPlan);

        return $this->adapter->request('GET', $location);
    }

    /**
     * {@inheritdoc}
     */
    public function postDocument($document, $database = null)
    {
        $location = $this->getDocumentLocation($database ?: $this->database);

        return $this->adapter->request('POST', $location, null, $document);
    }

    /**
     * {@inheritdoc}
     */
    public function putDocument($rid, $document, $database = null)
    {
        $location = $this->getDocumentLocation($database ?: $this->database, $rid);

        return $this->adapter->request('PUT', $location, null, $document);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDocument($rid, $version = null, $database = null)
    {
        $headers = null;

        if ($version) {
            $headers = array('If-Match' => $version);
        }

        $location = $this->getDocumentLocation($database ?: $this->database, $rid);

        return $this->adapter->request('DELETE', $location, $headers);
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
     * Returns the name of the current database in use.
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->database;
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
     * {@inheritdoc}
     */
    public function setAuthentication($username = null, $password = null)
    {
        $this->adapter->setAuthentication($username, $password);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdapter(HttpClientAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
