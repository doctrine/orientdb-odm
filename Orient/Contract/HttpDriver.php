<?php

/**
 * HttpDriver
 *
 * This interface is implemented by a class who wants to be the HttpDriver,
 * also simply known as 'client', of the binding.
 *
 * @package    Orient
 * @subpackage Contract
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