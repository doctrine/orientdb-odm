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

        $buffer = ob_start();
        echo $response;
        $toString = ob_get_contents();
        ob_end_flush();

        $this->assertEquals("AB", $toString);
    }

    public function testRetrieveTheWholeResponse()
    {
        $response = new Http\Response("A\r\n\r\nB");

        $this->assertEquals("AB", $response->getResponse());
    }
}
