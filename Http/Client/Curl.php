<?php

/**
 * Http client class
 *
 * @package    Orient
 * @subpackage Http
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Http\Client;

use Orient\Http\Response;
use Orient\Exception\Http\Response\Void as VoidResponse;

class Curl implements \Orient\Contract\Http\Client
{
  protected $client;
  protected $credential;
  protected $authentication;

  /**
   * Creates a new Curl instance.
   *
   * @param String $location
   */
  public function __construct($location = null)
  {
    $this->client = curl_init($location);
  }

  /**
   * Executes a Curl.
   *
   * @param   String $method
   * @param   String $location
   * @return  Response
   * @throws  Inconsistent
   */
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
    $this->restart();

    if (!$response)
    {
      throw new VoidResponse(__CLASS__, $location);
    }

    return new Response($response);
  }

  /**
   * Executes a DELETE on a resource.
   *
   * @param  String $location
   * @return Response
   */
  public function delete($location)
  {
    curl_setopt ($this->client, CURLOPT_CUSTOMREQUEST, 'DELETE');
    
    return $this->execute('DELETE', $location);
  }

  /**
   * GETs a resource.
   *
   * @param   String $location
   * @return  Response
   */
  public function get($location)
  {
    return $this->execute('GET', $location);
  }

  /**
   * Executes a POST on a location.
   *
   * @param   String $location
   * @param   String $body
   * @return  Response
   */
  public function post($location, $body)
  {
    curl_setopt ($this->client, CURLOPT_POST, 1);
    curl_setopt ($this->client, CURLOPT_POSTFIELDS, $body);

    return $this->execute('POST', $location);
  }

  /**
   * PUTs a resource.
   *
   * @param   String $location
   * @param   String $body
   * @return  Response
   */
  public function put($location, $body)
  {
    curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);

    return $this->execute('POST', $location);
  }

  /**
   * Sets the authentication string for the next HTTP requests.
   *
   * @param String $credential
   * @return String
   */
  public function setAuthentication($credential)
  {
    $this->authentication = $credential;

    return $this->authentication;
  }

  /**
   * Restarts the current cURL client
   */
  protected function restart()
  {
    curl_close($this->client);
    $this->client = curl_init();
  }
}

