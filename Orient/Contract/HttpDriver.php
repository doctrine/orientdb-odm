<?php

/**
 * Driver
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Contract;

interface HttpDriver
{
  function setAuthentication($credential);

  function execute($method, $location);

  function get($location);

  function post($location, $body);

  function delete($location);

  function put($location, $body);
}