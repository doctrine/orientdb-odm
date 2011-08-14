<?php

/**
 * TestCase class bound to Congow\Orient.
 *
 * @author Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\PHPUnit;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function assertCommandGives($expected, $got)
    {
        $message = "The raw command does not match the given SQL query";

        return $this->assertEquals($expected, $got, $message);
    }

    public function assertStatusCode($expected, \Congow\Orient\Http\Response $got)
    {
        $message = "The status code of the response is wrong";

        return $this->assertEquals($expected, $got->getStatusCode(), $got->getBody());
    }

    public function assertTokens($expected, $got)
    {
        $message = "The given command tokens do not match";

        return $this->assertEquals($expected, $got, $message);
    }
}
