<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Credential;

class Grant extends PHPUnit_Framework_TestCase
{
  public function testSchema()
  {
    $grant  = new \Orient\Query\Command\Credential\Grant();
    $queryClass   = get_class($grant);
    $tokens       = array(
        ':Permission'   => array(),
        ':Resource'     => array(),
        ':Role'         => array(),
    );

    $this->assertEquals(
      $tokens,
      $grant->getTokens(),
      'A GRANT should have the following schema: ' . $queryClass::SCHEMA
    );
  }

  public function testConstruct()
  {
    $grant = new \Orient\Query\Command\Credential\Grant();
    $query = 'GRANT ON TO';

    $this->assertEquals($query, $grant->getRaw());
  }

  public function testGrant()
  {
    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->grant('ALL');
    $query = 'GRANT ALL ON TO';

    $this->assertEquals($query, $grant->getRaw());

    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->grant('READ');
    $query = 'GRANT READ ON TO';

    $this->assertEquals($query, $grant->getRaw());
  }

  public function testOn()
  {
    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->on('resource');
    $query = 'GRANT ON resource TO';

    $this->assertEquals($query, $grant->getRaw());

    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->on('resource2');
    $query = 'GRANT ON resource2 TO';

    $this->assertEquals($query, $grant->getRaw());
  }

  public function testTo()
  {
    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->to('user');
    $query = 'GRANT ON TO user';

    $this->assertEquals($query, $grant->getRaw());

    $grant = new \Orient\Query\Command\Credential\Grant();
    $grant->to('user2');
    $query = 'GRANT ON TO user2';

    $this->assertEquals($query, $grant->getRaw());
  }
}

