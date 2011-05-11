<?php

/**
 * TestCase class bound to Orient.
 *
 * @author Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Test\PHPUnit;

class TestCase extends \PHPUnit_Framework_TestCase
{
  public function assertTokens($expected, $got)
  {
    $message = "The given command tokens do not match";
    
    return $this->assertEquals($expected, $got, $message);
  }
}

