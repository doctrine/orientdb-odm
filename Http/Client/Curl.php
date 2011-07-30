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
 * This class represents an HTTP client based on Curl.
 *
 * @package    Orient
 * @subpackage Http
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Http\Client;

use Orient\Http\Response;
use Orient\Exception\Http\Response\Void as VoidResponse;
use Orient\Contract\Http\Client as HttpClient;

class Curl implements HttpClient
{
    protected $client;
    protected $reuseHandle;
    protected $authentication;
    protected $timeout;

    /**
     * Creates a new Curl instance.
     */
    public function __construct($reuseHandle = false, $timeout = 2 )
    {
        $this->reuseHandle = $reuseHandle;
        $this->client = $this->createCurlHandle();
        $this->setTimeout($timeout);
    }
    
    /**
     * Closes the underlying cURL handle.
     */
    public function __destruct()
    {
        curl_close($this->client);
    }
    
    /**
     * Sets a timeout for the current cURL handler's requests.
     * 
     * @param integer $timeout
     */
    public function setTimeout($timeout)
    {
      curl_setopt($this->client, CURLOPT_TIMEOUT,$timeout);
    }

    /**
     * Create and initialize the underlying cURL handle.
     *
     * @return resource
     */
    protected function createCurlHandle()
    {
        $client = curl_init();

        $options = array(
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
        );
        if ($this->authentication) {
            $options[CURLOPT_USERPWD] = $this->authentication;
        }
        
        curl_setopt_array($client, $options);

        return $client;
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

        $response = curl_exec($this->client);

        if (!$this->reuseHandle) {
            $this->restart();
        }

        if (!$response) {
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
    public function delete($location, $body = NULL)
    {
        curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, "DELETE");

        if ($body) {
            curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);
        }

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
        curl_setopt($this->client, CURLOPT_POST, 1);
        curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);

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
        curl_setopt($this->client, CURLOPT_USERPWD, $this->authentication);

        return $this->authentication;
    }
    
    /**
     * Sets an HTTP header to send within the request.
     *
     * @param type $header
     * @param type $value 
     */
    public function setHeader($header, $value)
    {
      curl_setopt($this->client, CURLOPT_HTTPHEADER, array("$header: $value")); 
    }
    
    /**
     * Restarts the current cURL client
     */
    protected function restart()
    {
        curl_close($this->client);
        $this->client = $this->createCurlHandle();
    }

    /**
     * Sets whether to reuse the underlying cURL handle or use
     * a new one for each HTTP request.
     *
     * @param bool $value
     */
    public function reuseHandle($value)
    {
        $this->reuseHandle = $value;
    }
}
