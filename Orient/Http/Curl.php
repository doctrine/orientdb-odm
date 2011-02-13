<?php

/**
 * Request class
 *
 * @package    Orient
 * @subpackage Http
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Http;
use Orient\Contract;

class Curl implements Contract\HttpDriver
{
  protected $client;
  protected $credential;

  public function __construct($location = null)
  {
    $this->client = curl_init($location);
  }

  public function execute($method, $location)
  {
    curl_setopt($this->client, CURLOPT_URL, $location);

    if ($this->authentication)
    {
      curl_setopt($this->client, CURLOPT_USERPWD, $this->authentication);
    }
    
    curl_setopt($this->client, CURLOPT_HEADER, 1);
    curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($this->client);
    curl_close($this->client);

    if ($response)
    {
      return $response;
    }

    return false;
  }

  public function delete($location)
  {
    curl_setopt ($this->client, CURLOPT_CUSTOMREQUEST, 'DELETE');
    
    return $this->execute('DELETE', $location);
  }

  public function get($location)
  {
    return $this->execute('GET', $location);
  }

  public function post($location, $body)
  {
    curl_setopt ($this->client, CURLOPT_POST, 1);
    curl_setopt ($this->client, CURLOPT_POSTFIELDS, $body);

    return $this->execute('POST', $location);
  }

  public function put($location, $body)
  {
    curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);

    return $this->execute('POST', $location);
  }

  public function setAuthentication($credential)
  {
    $this->authentication = $credential;
  }
}

