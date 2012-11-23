<?php

/**
 * CurlClientTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Client\Http\CurlClient;

class CurlClientTest extends TestCase
{
    /**
     * @fixes https://github.com/congow/Orient/pull/97
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
     * @expectedException Doctrine\OrientDB\Exception\Http\Response\Void
     */
    public function testRetrievingAnEmptyResponseRaisesAnException()
    {
        $client = new CurlClient();

        $client->execute('GET', '');
    }
}
