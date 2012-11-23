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
 * Adapter for the standard HTTP client that comes with the library.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding\Adapter;

use Doctrine\OrientDB\Binding\Client\Http\CurlClient;

class CurlClientAdapter implements HttpClientAdapterInterface
{
    protected $client;

    /**
     * @param CurlClient $client
     */
    public function __construct(CurlClient $client = null)
    {
        $this->client = $client ?: new CurlClient();
    }

    /**
     * {@inheritdoc}
     */
    public function request($method, $location, array $headers = null, $body = null)
    {
        if ($headers) {
            foreach ($headers as $k => $v) {
                $this->client->setHeader($k, $v);
            }
        }

        switch (strtoupper($method)) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                $response = $this->client->$method($location, $body);
                break;
            default:
                $response = $this->client->$method($location);
                break;
        }

        return new CurlClientAdapterResult($response);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthentication($username, $password)
    {
        $credential = !isset($username, $password) ? null : "$username:$password";
        $this->client->setAuthentication($credential);
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->client;
    }
}
