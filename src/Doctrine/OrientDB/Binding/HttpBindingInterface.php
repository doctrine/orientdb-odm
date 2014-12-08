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
 * This interface is implemented in order to be compliant with the interface
 * Doctrine\OrientDB exposes through its HTTP interface.
 * See: http://code.google.com/p/orient/wiki/Doctrine\OrientDB_REST
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding;

use Doctrine\OrientDB\Binding\Adapter\HttpClientAdapterInterface;

interface HttpBindingInterface extends BindingInterface
{
    /**
     * Deletes a class.
     *
     * @api
     * @param string $class
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteClass($class, $database = null);

    /**
     * Retrieves a class and its records.
     *
     * @api
     * @param string $class
     * @param string $database
     * @return BindingResultInterface
     */
    public function getClass($class, $database = null);

    /**
     * Creates a new class.
     *
     * @api
     * @param string $class
     * @param string $body
     * @param string $database
     * @return BindingResultInterface
     */
    public function postClass($class, $body = null, $database = null);

    /**
     * Retrieves records from the given cluster in the database.
     *
     * @api
     * @param   string  $cluster
     * @param   string  $database
     * @param   integer $limit
     * @return BindingResultInterface
     */
    public function cluster($cluster, $limit = null, $database = null);

    /**
     * Connects to the specified database.
     *
     * @api
     * @param string $database
     * @return BindingResultInterface
     */
    public function connect($database);

    /**
     * Disconnect this instance from the server.
     *
     * @api
     * @return BindingResultInterface
     */
    public function disconnect();

    /**
     * Gets the current server.
     *
     * @api
     * @return BindingResultInterface
     */
    public function getServer();

    /**
     * Creates a new database.
     *
     * @api
     * @param string $database
     * @param string $storage
     * @param string $type
     * @return BindingResultInterface
     */
    public function createDatabase($database, $storage = 'memory', $type = 'document');

    /**
     * Lists all the existing databases.
     *
     * @api
     * @return BindingResultInterface
     */
    public function listDatabases();

    /**
     * Deletes an existing database.
     *
     * @api
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteDatabase($database);

    /**
     * Executes a raw command on the given database.
     *
     * @api
     * @param string $query
     * @param string $language
     * @param string $database
     * @return BindingResultInterface
     */
    public function command($query, $language = BindingInterface::LANGUAGE_SQLPLUS, $database = null);

    /**
     * Executes a raw query on the given database.
     *
     * Results can be limited with the $limit parameter and a fetch plan can be used to
     * specify how to retrieve the graph and limit its depth.
     *
     * It differs from the command because OrientDB defines a query as a SELECT only.
     *
     * @api
     * @param string $query SQL or Gremlin query.
     * @param int $limit Maximum number of records (default is 20).
     * @param string $fetchPlan
     * @param string $language
     * @param string $database
     * @return BindingResultInterface
     */
    public function query($query, $limit = null, $fetchPlan = null, $language = BindingInterface::LANGUAGE_SQLPLUS, $database = null);

    /**
     * Retrieves a record from the database. An optional fetch plan can be used to
     * specify how to retrieve the graph and limit its depth.
     *
     * @api
     * @param string $rid
     * @param string $database
     * @param string $fetchPlan
     * @return BindingResultInterface
     */
    public function getDocument($rid, $database = null, $fetchPlan = null);

    /**
     * Stores a new document in the database.
     *
     * @api
     * @param string $document
     * @param string $database
     * @return BindingResultInterface
     */
    public function postDocument($document, $database = null);

    /**
     * Updates an existing document in the database.
     *
     * @api
     * @param string $rid
     * @param string $document
     * @param string $database
     * @return BindingResultInterface
     */
    public function putDocument($rid, $document, $database = null);

    /**
     * Deletes a document from the database.
     *
     * @api
     * @param string $rid
     * @param string $version
     * @param string $database
     * @return BindingResultInterface
     */
    public function deleteDocument($rid, $version = null, $database = null);

    /**
     * Sets the username and password used to authenticate to the server.
     *
     * @param string $username
     * @param string $password
     */
    public function setAuthentication($username = null, $password = null);

    /**
     * Sets the underlying HTTP client adapter.
     *
     * @param HttpClientAdapterInterface $client
     */
    public function setAdapter(HttpClientAdapterInterface $adapter);

    /**
     * Sets the underlying HTTP client adapter.
     *
     * @return HttpClientAdapterInterface
     */
    public function getAdapter();
}
