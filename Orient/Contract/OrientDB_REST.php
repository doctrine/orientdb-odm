<?php

/**
 * OrientDB_REST interface
 *
 * @package    Orient
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Contract;

interface OrientDB_REST
{
  function __construct($hostname, $port, $username, $password);

  function connect($database, $method);

  function getHttpDriver();

  function setHttpDriver(HttpDriver $driver);
}