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
 * This interface is implemented in order to be compliant with the interface
 * Congow\Orient exposes through its HTTP interface.
 * See: http://code.google.com/p/orient/wiki/Congow\OrientDB_REST
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Protocol;

use Congow\Orient\Contract\Http\Client;

interface Http
{
    /**
     * Executes an HTTP requests in order to delete the given $class in the
     * $database.
     *
     * @param   string  $class
     * @param   string  $database
     * @return  mixed
     */
    public function deleteClass($class, $database = false);

    /**
     * Executes an HTTP requests in order to retrieve the given $class in the
     * $database.
     *
     * @param   string  $class
     * @param   string  $database
     * @return  mixed
     */
    public function getClass($class, $database = false);

    /**
     * Executes an HTTP requests in order to create a $class in the
     * $database.
     *
     * @param   string  $class
     * @param   string  $database
     * @return  mixed
     */
    public function postClass($class, $database = false, $body = null);

    /**
     * Executes an HTTP requests in order to retrieve records of the given
     * $cluster in the $database.
     * You optionally specify a $limit for the amount of records.
     *
     * @param   string  $cluster
     * @param   string  $database
     * @param   integer $limit
     * @return  mixed
     */
    public function cluster($cluster, $database = false, $limit = null);

    /**
     * Opens a connection to an Congow\OrientDB $database.
     *
     * @param   string  $database
     * @return  mixed
     */
    public function connect($database);

    /**
     * Disconnects the client from the Congow\OrientDB database, if any connection
     * is still open.
     *
     * @return  mixed
     */
    public function disconnect();

    /**
     * Retrieves informations about the running Congow\OrientDB server instance.
     *
     * @return  mixed
     */
    public function getServer();

    /**
     * Executes a raw SQL command on the given $database.
     *
     * @param   string  $sql
     * @param   string  $database
     * @return  mixed
     */
    public function command($sql, $database = null);

    /**
     * Retrieves informations about the given $database.
     *
     * @param   string  $database
     * @return  mixed
     */
    public function getDatabase($database = null);

    /**
     * Executes a raw SQL query on the given $database.
     * Results can be limited with the $limit parameter and a $fetchplan
     * can be specified in order to limit graph depth.
     * It differs from the <code>command()</code> because queries are read-only
     * SQL statements (so a subset of the command).
     *
     * @param   string  $sql
     * @param   string  $database
     * @param   integer $limit
     * @param   string  $fetchPlan
     * @return  mixed
     */
    public function query($sql, $database = null, $limit = null, $fetchPlan = null);

    /**
     * Retrieves a document in the given $database from its $rid.
     * A $fetchPlan can be specified in order to decide object's graph size.
     *
     * @param   string  $rid
     * @param   string  $database
     * @param   string  $fetchPlan
     * @return  mixed
     */
    public function getDocument($rid, $database = null, $fetchPlan = null);

    /**
     * Creates a new document from the input $document in the given $database.
     *
     * @param   json    $document
     * @param   string  $database
     * @return  mixed
     */
    public function postDocument($document, $database = null);

    /**
     * Updates the existing $document identified by the given $rid on $database.
     *
     * @param   string  $rid
     * @param   string  $document
     * @param   string  $database
     * @return  mixed
     */
    public function putDocument($rid, $document, $database = null);

    /**
     * Deletes the document identified by the given $rid in the $database.
     *
     * @param   string  $rid
     * @param   string  $database
     * @return  mixed
     */
    public function deleteDocument($rid, $document, $database = null);

    /**
     * Sets the $username and $password used in the digest HTTP authentication.
     *
     * @param   string  $username
     * @param   string  $password
     * @return  mixed
     */
    public function setAuthentication($username = null, $password = null);

    /**
     * Retrieves the authentication credentials in the form of
     * <code>username:password</code>
     *
     * @return  string
     */
    public function getAuthentication();

    /**
     * Sets the default $database to operate with.
     * Since almost all the methods exposed by Congow\OrientDB HTTP interface let you
     * specify the DB at each request, doing it in the application code is
     * boring, and you can use this method to set the default DB used to work
     * with Congow\OrientDB.
     *
     * @param   string  $database
     * @return  mixed
     */
    public function setDatabase($database);

    /**
     * Sets the internal object which handles the "real" HTTP requests.
     *
     * @param   Client  $client
     * @return  mixed
     */
    public function setHttpClient(Client $client);

    /**
     * Retrieve the internal object used to perform HTTP requests.
     *
     * @return  Client
     */
    public function getHttpClient();
}
