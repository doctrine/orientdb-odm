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
    $this->create  = new Create('p', 'c');
  }

  public function testTheSchemaIsValid()
  {
    $tokens       = array(
        ':IndexClass' => array(),
        ':Property'   => array(),
        ':Type'       => array(),
    );

    $this->assertTokens($tokens, $this->create->getTokens());
  }

  public function testConstructionOfAnObject()
  {
    $query = 'CREATE INDEX c.p';

    $this->assertCommandGives($query, $this->create->getRaw());
  }

  public function testConstructionOfAnIndexWithoutClass()
  {
    $query = 'CREATE INDEX p';
    $this->create  = new Create('p');

    $this->assertCommandGives($query, $this->create->getRaw());
  }

  public function testSettingTheIndexType()
  {
    $query = 'CREATE INDEX p string';
    $this->create  = new Create('p', NULL, 'string');

    $this->assertCommandGives($query, $this->create->getRaw());
  }

  public function testSettingTheIndexTypeWithTheFluentInterface()
  {
    $query = 'CREATE INDEX c.p string';

    $this->assertCommandGives($query, $this->create->type('string')->getRaw());
  }
}

