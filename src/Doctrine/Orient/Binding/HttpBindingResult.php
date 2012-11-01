<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * HTTP bindings results set.
 *
 * @package    Doctrine\Orient
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\Orient\Binding;

use Doctrine\Orient\Exception\Query\SQL\Invalid as InvalidSQL;
use Doctrine\Orient\Contract\Binding\HttpBindingResultInterface;

class HttpBindingResult implements HttpBindingResultInterface
{
    protected $response;

    /**
     * @param mixed $response Original response object
     */
    public function __construct($response)
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
