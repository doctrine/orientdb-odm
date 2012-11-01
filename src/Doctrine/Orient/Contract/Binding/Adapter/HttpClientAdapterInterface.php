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
 * Interface for adapters to various HTTP client libraries and classes.
 *
 * @package    Doctrine\Orient
 * @subpackage Contract
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\Orient\Contract\Binding\Adapter;

use Doctrine\Orient\Contract\Binding\BindingResultInterface;

interface HttpClientAdapterInterface
{
    /**
     * Performs an HTTP request on the specified location.
     *
     * @param   string $method
     * @param   string $location
     * @param   array  $headers
     * @param   string $body
     * @return  BindingResultInterface
     */
    public function request($method, $location, array $headers = null, $body = null);

    /**
     * Sets the username and password used to authenticate to the server.
     *
     * @param string $username Username
     * @param string $password Password
     */
    public function setAuthentication($username, $password);

    /**
     * Returns the underlying client instance wrapped by the adapter.
     *
     * @return mixed
     */
    public function getClient();
}
