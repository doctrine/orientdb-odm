<?php

/**
 * ResponseTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
use Orient\Http;

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testToString()
    {
        $response = new Http\Response("A\r\n\r\nB");

        $this->assertEquals("AB", (string) $response);
    }

    public function testRetrieveTheWholeResponse()
    {
        $response = new Http\Response("A\r\n\r\nB");

        $this->assertEquals("AB", $response->getResponse());
    }

    public function testExtractBodyCorrectlyFromResponse()
    {
        $response = new Http\Response("A\r\n\r\nB\r\n\r\nC");

        $this->assertEquals("B\r\n\r\nC", $response->getBody());
    }
}
