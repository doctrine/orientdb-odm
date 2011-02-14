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
   * @param String $database
   * @return mixed
   */
  public function class_($class, $database = false, $method = 'GET', $body = null)
  {
    $this->database = $database ?: $this->database;
    $method         = strtolower($method);
    $location       = $this->server . '/class/' . $this->database . '/' . $class;
    $this->checkDatabase(__METHOD__);

    // TODO: better a strategy here?
    if ($method == 'get')
    {
      return $this->getHttpDriver()->get($location);
    }
    elseif ($method == 'post')
    {
      return $this->getHttpDriver()->post($location, $body);
    }

    // TODO: Implement a 405 response
  }

  /**
   * @param String $database
   * @return mixed
   */
  public function connect($database)
  {
    return $this->getHttpDriver()->get($this->server . '/database/' . $database);
  }

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

  protected function checkDatabase($method)
  {
    if (!is_null($this->database))
    {
      return true;
    }

    throw new \Exception(sprintf('In order to perform a %s you must specify a database', $method));
  }
}

