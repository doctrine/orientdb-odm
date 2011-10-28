<?php

/**
 * CurlTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Http\Client;

use test\PHPUnit\TestCase;
use Congow\Orient\Http\Client\Curl;

class CurlTest extends TestCase
{
    public function setup()
    {
        $this->client = new Curl();
    }
    
    /**
     * @fixes https://github.com/congow/Orient/pull/97
     */
    public function testYouCanExecuteAGETAfteraPOST()
    {
        $this->client->post('http://www.google.com/', array());
        $this->client->get('http://www.google.com/');
        
        $this->assertFalse(curl_getinfo($this->client->client, CURLINFO_HTTP_CODE) == 405);
    }
}