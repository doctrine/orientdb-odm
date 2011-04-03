<?php

/**
 * Binding class
 *
 * This class is the foundation of the library : it's the low-level binding
 * connecting to Orient.
 * It's also responsible of incapsulating a proper client which requests to
 * Orient.
 *
 * @package    Orient
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Foundation;

use Orient\Http;
use Orient\Contract;

class Binding implements Contract\OrientDB_REST
{
  protected $server;
  protected $driver;
  protected $username;
  protected $password;
  protected $authentication;

  /**
   * @param String $host
   * @param String $port
   * @param String $username
   * @param String $password
   */
  function  __construct(Contract\HttpDriver $driver, $host = '127.0.0.1', $port = 2480, $username = null, $password = null, $database = null)
  {
    $this->server   = $host . ($port ? sprintf(':%s', $port) : false) ;
    $this->username = $username;
    $this->password = $password;
    $this->database = $database;
    $this->driver   = $driver;

    $this->setAuthentication($username, $password);
  }

  /**
   * Deletes a class.
   *
   * @param String $class
   * @param String $database
   * @return Http\Response
   */
  public function deleteClass($class, $database = false)
  {
    $this->resolveDatabase($database);
    $location       = $this->server . '/class/' . $this->database . '/' . $class;

    return $this->getHttpDriver()->delete($location);
  }

  /**
   * Gets a class and its records.
   *
   * @param String $class
   * @param String $database
   * @return Http\Response
   */
  public function getClass($class, $database = false)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/class/' . $this->database . '/' . $class;

    return $this->getHttpDriver()->get($location);
  }

  /**
   * Creates a new class.
   *
   * @param String $class
   * @param String $database
   * @param String $body
   * @return Http\Response
   */
  public function postClass($class, $database = false, $body = null)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/class/' . $this->database . '/' . $class;

    return $this->getHttpDriver()->post($location, $body);
  }
  
  /**
   * Gets informations about a cluster.
   *
   * @param String $cluster
   * @param boolean $database
   * @return mixed
   */
  public function cluster($cluster, $database = false, $limit = null)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/cluster/'. $this->database .'/' . $cluster . ($limit ? '/' . $limit : '') ;

    return $this->getHttpDriver()->get($location);
  }

  /**
   * Connects the instance to a DB.
   *
   * @param String $database
   * @return mixed
   */
  public function connect($database)
  {
    return $this->getHttpDriver()->get($this->server . '/database/' . $database);
  }

  /**
   * Disconnect this instance from the server.
   *
   * @return Http\Response
   */
  public function disconnect()
  {
    return $this->getHttpDriver()->get($this->server . '/disconnect');
  }

  /**
   * Gets the current server
   *
   * @return Http\Response
   */
  public function getServer()
  {
    return $this->getHttpDriver()->get($this->server . '/server');
  }

  /**
   * Executes a raw SQL query on the given DB.
   *
   * @param String $sql
   * @param String $database
   * @return Http\Response
   */
  public function command($sql, $database = null)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/command/' . $this->database . '/sql/' . urlencode($sql);

    return $this->getHttpDriver()->post($location, null);
  }

  /**
   * Gets informations about a DB.
   *
   * @param String $database
   * @return Http\Response
   */
  public function getDatabase($database = null)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/database/' . $this->database;

    return $this->getHttpDriver()->get($location);
  }

  /**
   * Executes a raw query. It differs from the command because Orient defines
   * a query a a SELECT only.
   *
   * @param String $sql       The query
   * @param String $database
   * @param Int $limit        Results limit, default 20
   * @param String $fetchPlan 
   * @return Orient\Response
   */
  public function query($sql, $database = null, $limit = null, $fetchPlan = null)
  {
    $this->resolveDatabase($database);
    $location = $this->server . '/query/' . $this->database . '/sql/' . urlencode($sql);

    if ($limit)
    {
      $location .= '/' . (int) $limit;

      if ($fetchPlan)
      {
        $location .= '/' . $fetchPlan;
      }
    }

    return $this->getHttpDriver()->get($location);
  }

  /**
   * Assigns a database to the current instance.
   *
   * @param String $database
   */
  protected function resolveDatabase($database = false)
  {
    $this->database = $database ?: $this->database;
    $this->checkDatabase(__METHOD__);
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
   * @param String $username
   * @param String $password
   * @return bool
   */
  public function setAuthentication($username = null, $password = null)
  {
    $this->username = $username ?: $this->username;
    $this->password = $password ?: $this->password;
    $this->authentication = sprintf('%s:%s', $this->username, $this->password);

    if ($this->authentication === ':')
    {
      $this->authentication = false;
    }

    $this->getHttpDriver()->setAuthentication($this->authentication);
    
    return $this->authentication;
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
   * Injects the HttpDriver instance inside the binding.
   *
   * @param Contract\HttpDriver $driver
   */
  public function setHttpDriver(Contract\HttpDriver $driver)
  {
    $this->driver = $driver;
  }

  /**
   * Returns the HttpDriver of the binding.
   *
   * @return Contract\HttpDriver
   */
  public function getHttpDriver()
  {
    if ($this->driver instanceOf Contract\HttpDriver)
    {
      return $this->driver;
    }

    throw new \Exception('You must inject an http driver to the Orient instance via setHttpDriver');
  }

  /**
   * Checks wheter the current object is able to perform a request on a DB.
   *
   * @return true
   * @throws Exception
   */
  protected function checkDatabase()
  {
    if (!is_null($this->database))
    {
      return true;
    }

    throw new \Exception(sprintf('In order to perform the operation you must specify a database'));
  }
}

