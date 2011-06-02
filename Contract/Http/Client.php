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
 * This interface is implemented by a class who wants to act as an HTTP client
 * for the \Orient\Foundation\Binding.
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Http;

interface Client
{
    /**
     * Sets the HTTP authentication $credential, in the form of
     * username:password.
     *
     * @param   string  $credential
     */
    public function setAuthentication($credential);

    /**
     * Executes a generic HTTP request with the given protocol $method at the
     * specified $location.
     *
     * @param   string  $method
     * @param   string  $location
     * @return  mixed
     */
    public function execute($method, $location);

    /**
     * Performs a GET request on the specified $location.
     *
     * @param   string  $location
     * @return  mixed
     */
    public function get($location);

    /**
     * Performs a POST request on the specified $location, with the given $body.
     *
     * @param   string  $location
     * @param   string  $body
     * @return  mixed
     */
    public function post($location, $body);

    /**
     * Performs a DELETE request on the specified $location.
     *
     * @param   string  $location
     * @return  mixed
     */
    public function delete($location);

    /**
     * Performs a PUT request on the specified $location, with the given $body.
     *
     * @param   string  $location
     * @param   string  $body
     * @return  mixed
     */
    public function put($location, $body);
}