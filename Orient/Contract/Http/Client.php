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
namespace Orient\Contract\Http;

interface Client
{
  public function setAuthentication($credential);

  public function execute($method, $location);

  public function get($location);

  public function post($location, $body);

  public function delete($location);

  public function put($location, $body);
}