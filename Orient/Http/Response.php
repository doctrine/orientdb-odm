<?php

/**
 * Response class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Http;

class Response
{
  protected $headers;
  protected $raw_headers;
  protected $status_code;
  protected $body;
  protected $response;

  /**
   * Constructs a new object from an existing HTTP response.
   *
   * @param String $response
   */
  public function __construct($response)
  {
    $parts = explode("\r\n\r\n", $response);

    $headers            = array_key_exists(0, $parts) ? $parts[0] : null;
    $this->body         = array_key_exists(1, $parts) ? $parts[1] : null;
    $this->raw_headers  = $headers;
    $this->headers      = $this->buildHeaders($this->raw_headers);
  }

  public function __toString()
  {
    return $this->getResponse();
  }

  /**
   * Returns the body of the response.
   *
   * @return String
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Returns the whole response.
   *
   * @return String
   */
  public function getResponse()
  {
    return $this->getRawHeaders() . $this->getBody();
  }

  /**
   * Builds headers array from a well-formatted string.
   *
   * @param String $headers
   */
  public function buildHeaders($headers)
  {
    $parts = explode("\r\n", $headers);

    $this->status_code = $parts[0];
    unset($parts[0]);

    foreach ($parts as $header)
    {
      $header = explode(':', $header);
      $this->headers[$header[0]] = $header[1];
    }
  }

  /**
   * Returns the status code of the response.
   *
   * @return String
   */
  public function getStatusCode()
  {
    return $this->status_code;
  }

  /**
   * Returns all the headers as a string.
   *
   * @return String
   */
  protected function getRawHeaders()
  {
    return $this->raw_headers;
  }
}

