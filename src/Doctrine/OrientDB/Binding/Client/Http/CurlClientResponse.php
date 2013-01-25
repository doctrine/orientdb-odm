<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class wraps an HTTP response returned by the Curl client.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding\Client\Http;

class CurlClientResponse
{
    protected $headers;
    protected $rawHeaders;
    protected $statusCode;
    protected $body;
    protected $protocol;

    const STATUS_OK                             = 200;
    const STATUS_CREATED                        = 201;
    const STATUS_ACCEPTED                       = 202;
    const STATUS_NON_AUTHORITATIVE_INFORMATION  = 203;
    const STATUS_NO_CONTENT                     = 204;
    const STATUS_RESET_CONTENT                  = 205;
    const STATUS_PARTIAL_CONTENT                = 206;

    /**
     * Constructs a new object from an existing HTTP response.
     *
     * @param String $response
     */
    public function __construct($response)
    {
        @list($this->rawHeaders, $this->body) = explode("\r\n\r\n", $response, 2);

        $this->buildHeaders($this->rawHeaders);
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
     * Returns the value of the specified header.
     *
     * @param string $header Header name.
     * @return string
     */
    public function getHeader($header)
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : null;
    }

    /**
     * Returns the cookies set in the current response.
     *
     * @return array
     */
    public function getCookies()
    {
        $jar = array();
        $headers = $this->getRawHeaders();
        if (preg_match_all('/Set-Cookie: (.*)\b/', $headers, $cookies)) {
            foreach ($cookies[1] as $cookie) {
                list($cookie,) = explode(';', $cookie, 2);
                list($name, $value) = explode('=', $cookie, 2);
                $jar[$name] = $value;
            }
        }
        return $jar;
    }

    /**
     * Returns the whole response.
     *
     * @return String
     */
    public function getResponse()
    {
        return $this->getRawHeaders() . "\r\n\r\n" . $this->getBody();
    }

    /**
     * Returns an array for the HTTP status codes that
     * are considered as valid responses.
     *
     * @return String
     */
    public function getValidStatusCodes()
    {
        return array(
            self::STATUS_OK,
            self::STATUS_ACCEPTED,
            self::STATUS_NON_AUTHORITATIVE_INFORMATION,
            self::STATUS_NO_CONTENT,
            self::STATUS_RESET_CONTENT,
            self::STATUS_PARTIAL_CONTENT,
            self::STATUS_CREATED
        );
    }

    /**
     * Builds headers array from a well-formatted string.
     *
     * @param string $headers
     */
    protected function buildHeaders($headers)
    {
        $parts = explode("\r\n", $headers);
        $status = array_shift($parts);
        $statusParts = explode(' ', $status);

        if (array_key_exists(0, $statusParts)) {
            $this->setProtocol($statusParts[0]);
        }

        if (array_key_exists(1, $statusParts)) {
            $this->setStatusCode($statusParts[1]);
        }

        foreach ($parts as $header) {
            list($header, $value)   = explode(':', $header, 2);
            $header                 = trim($header, ' ');

            if (isset($this->headers[$header])) {
                $this->headers[$header] .= "," . $value;
            } else {
                $this->headers[$header] = trim($value, ' ');
            }
        }
    }

    /**
     * Returns all the headers as a string.
     *
     * @return string
     */
    protected function getRawHeaders()
    {
        return $this->rawHeaders;
    }

    /**
     * Sets the protocol used for the communication with the client.
     *
     * @param string $protocol
     */
    protected function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * Returns the protocol used to communicate with the client.
     *
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Sets the status code of the response.
     *
     * @param integer $code
     */
    protected function setStatusCode($code)
    {
        $this->statusCode = (int) $code;
    }

    /**
     * Returns the status code of the response.
     *
     * @return String
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
