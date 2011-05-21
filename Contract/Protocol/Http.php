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
 * OrientDB_REST interface
 *
 * This interfaces is implemented in order to be compliant with the interface
 * Orient exposes through quite RESTful "services".
 * See: http://code.google.com/p/orient/wiki/OrientDB_REST
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Contract\Protocol;

use Orient\Contract\Http\Client;

interface Http
{
  public function __construct(Client $driver, $hostname, $port, $username, $password);

  public function deleteClass($class, $database = false);

  public function getClass($class, $database = false);

  public function postClass($class, $database = false, $body = null);

  public function cluster($cluster, $database = false, $limit = null);

  public function connect($database);

  public function disconnect();

  public function getServer();

  public function command($sql, $database = null);

  public function getDatabase($database = null);

  public function query($sql, $database = null, $limit = null, $fetchPlan = null);

  public function getDocument($rid, $database = null, $fetchPlan = null);

  public function postDocument($document, $database = null);

  public function putDocument($rid, $document, $database = null);

  public function deleteDocument($rid, $database = null);

  public function setAuthentication($username = null, $password = null);

  public function getAuthentication();

  public function setDatabase($database);

  public function setHttpClient(Client $client);

  public function getHttpClient();
}