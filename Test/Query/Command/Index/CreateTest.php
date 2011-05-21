<?php

/**
 * CreateTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Index;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\Index\Create;

class CreateTest extends TestCase
{
  public function setup()
  {
    $this->create  = new Create('c', 'p');
  }

  public function testTheSchemaIsValid()
  {
    $tokens       = array(
        ':Class'   => array(),
        ':Property'   => array(),
    );

    $this->assertTokens($tokens, $this->create->getTokens());
  }

  public function testConstructionOfAnObject()
  {
    $query = 'CREATE INDEX c.p';

    $this->assertCommandGives($query, $this->create->getRaw());
  }
}

