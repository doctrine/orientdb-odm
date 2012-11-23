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
 * Binding result class that wraps a response from the Curl HTTP client.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding\Adapter;

use Doctrine\OrientDB\Binding\HttpBindingResultInterface;
use Doctrine\OrientDB\Client\Http\CurlClientResponse;
use Doctrine\OrientDB\Exception\Query\SQL\Invalid as InvalidSQL;

class CurlClientAdapterResult implements HttpBindingResultInterface
{
    protected $response;

    /**
     * @param mixed $response Response object.
     */
    public function __construct(CurlClientResponse $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult()
    {
        if (!$this->isValid()) {
            throw new InvalidSQL($this->response->getBody(), $this);
        }

        if (false === $json = json_decode($this->response->getBody())) {
            throw new \RuntimeException("Invalid JSON payload");
        }

        return isset($json->result) ? $json->result : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return in_array($this->response->getStatusCode(), $this->response->getValidStatusCodes());
    }

    /**
     * {@inheritdoc}
     */
    public function getInnerResponse()
    {
        return $this->response;
    }
}
