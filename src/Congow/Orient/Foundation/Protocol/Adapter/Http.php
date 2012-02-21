<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Http
 *
 * @package     Orient
 * @subpackage  Foundation
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Foundation\Protocol\Adapter;

use Congow\Orient\Contract\Protocol\Adapter as ProtocolAdapter;
use Congow\Orient\Query\Command\Select;
use Congow\Orient\Contract\Http\Client;
use Congow\Orient\Foundation\Binding;
use Congow\Orient\Exception\Query\SQL\Invalid as InvalidSQL;
use Congow\Orient\Exception\Http\Response\Void as VoidResponse;
use Congow\Orient\Query;
use Congow\Orient\Http\Response;

class Http implements ProtocolAdapter
{
    
    protected $client;
    protected $result;
    
    /**
     * Instantiates a new adapter.
     *
     * @param Http\Client $client
     * @param String $host
     * @param String $port
     * @param String $username
     * @param String $password
     */
    public function __construct(Binding $binding)
    {
        $this->client = $binding;
    }
    
    /**
     * Executes some SQL against Congow\OrientDB via the HTTP binding.
     * @todo    fix $result
     * @param   Congow\Orient\Query $query
     * @return  mixed
     */
    public function execute(Query $query, $fetchPlan = null)
    {   
        if ($query->getCommand() instanceOf Select) {            
            $response = $this->getClient()->query($query->getRaw(), null, -1, $fetchPlan);
        } else {
            $response = $this->getClient()->command($query->getRaw());
        }

        $this->checkResponse($response);

        if ($query->shouldReturn()) {

            $body = json_decode($response->getBody());

            if ($response->getHeader('Content-Type') == 'text/plain' ) {

                //if numeric the result is provided by a update command
                if (!is_numeric($body)) {
                    $result = explode('#', $response->getBody());
                    $result = explode('{', $result[1]);
                    $result = $result[0];
                }else{
                    $result = $response->getBody();
                }
                
                $this->setResult($result);

            } else {
                $this->setResult($body->result);
            }
        }   

        return true;
    }
    
    /**
     * Returns OrientDB's response to an HTTP request.
     * 
     * Pseudo code:
     * <code>
     * if ($adapter->execute('SELECT...')) {
     *   foreach ($adapter->getResult() as $records) {
     *     ...
     *   }
     * }
     * </code>
     * 
     * @see    Congow\Orient\Contract\Protocol\Adapter::getResult
     * @return Array|null
     */
    public function getResult()
    {
        return $this->result;
    }
    
    /**
     * Checks whether the $response is valid.
     * A response is *not* considere valid when:
     * * it's void
     * * it returns a status code different from 2XX
     *
     * @param Response $response 
     */
    protected function checkResponse(Response $response = null)
    {
        if (!$response) {
            throw new VoidResponse(get_class($this->getClient()), $sql);
        }

        if (!in_array($response->getStatusCode(), $response->getValidStatusCodes())) {
            throw new InvalidSQL($response);   
        }
    }
    
    /**
     * Returns the internal client used to make requests to OrientDB.
     *
     * @return Congow\Orient\Contract\Http\Client
     */
    protected function getClient()
    {
        return $this->client;
    }
    
    /**
     * Sets the result of the execute() method.
     *
     * @param mixed $result 
     */
    protected function setResult($result)
    {
        $this->result = $result;
    }
}

