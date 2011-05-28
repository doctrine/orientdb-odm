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
 * This class wraps an HTTP response to easily manage some HTTP headers and
 * the body.
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
        list($this->raw_headers, $this->body) = explode("\r\n\r\n", $response, 2);

        $this->buildHeaders($this->raw_headers);
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
     * Returns the status code of the response.
     *
     * @return String
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Builds headers array from a well-formatted string.
     *
     * @param String $headers
     */
    protected function buildHeaders($headers)
    {
        $parts = explode("\r\n", $headers);

        $this->status_code = $parts[0];
        unset($parts[0]);

        foreach ($parts as $header) {
            $header = explode(':', $header);
            $this->headers[$header[0]] = $header[1];
        }
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
