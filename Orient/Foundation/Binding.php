<?php

/**
 * Binding class
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

  function  __construct($host = '127.0.0.1', $port = 2480, $username = null, $password = null)
  {
    $this->server   = $host . ($port ? sprintf(':%s', $port) : false) ;
    $this->username = $username;
    $this->password = $password;

    $this->setAuthentication($username, $password);
  }

  public function connect($database)
  {    
    return $this->getHttpDriver()->get($this->server . '/database/' . $database);
  }

  public function getAuthentication()
  {
    return $this->authentication;
  }

  public function setAuthentication($username = null, $password = null)
  {
    $this->username = $username ?: $this->username;
    $this->password = $password ?: $this->password;
    $this->authentication = sprintf('%s:%s', $this->username, $this->password);

    if ($this->authentication === ':')
    {
      $this->authentication = false;
    }

    return $this->authentication;
  }

  public function setHttpDriver(Contract\HttpDriver $driver)
  {
    $this->driver = $driver;
    $this->driver->setAuthentication($this->authentication);
  }

  public function getHttpDriver()
  {
    if ($this->driver instanceOf Contract\HttpDriver)
    {
      return $this->driver;
    }

    throw new \Exception('You must inject an http driver to the Orient instance via setHttpDriver');
  }
}

