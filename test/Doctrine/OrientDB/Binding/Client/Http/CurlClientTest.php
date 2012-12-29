<?php

/**
 * CurlClientTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Binding\Client\Http;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Binding\Client\Http\CurlClient;

class CurlClientTest extends TestCase
{
    /**
     * @fixes https://github.com/doctrine/orientdb-odm/pull/97
     *
     * Test coupled with a Google response
     */
    public function testYouCanExecuteAGETAfteraPOST()
    {
        $client = new CurlClient();

        $client->post('http://www.google.com/', array());
        $response = $client->get('http://www.google.com/');

        $this->assertFalse($response->getStatusCode() == 411);
    }

    /**
     * @expectedException Doctrine\OrientDB\Binding\Client\Http\EmptyResponseException
     */
    public function testRetrievingAnEmptyResponseRaisesAnException()
    {
        $client = new CurlClient();

        $client->execute('GET', '');
    }
}
