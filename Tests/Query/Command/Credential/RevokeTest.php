<?php

/**
 * RevokeTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
class RevokeTest extends PHPUnit_Framework_TestCase
{
  public function testSchema()
  {
    $revoke  = new \Orient\Query\Command\Credential\Revoke();
    $queryClass   = get_class($revoke);
    $tokens       = array(
        ':Permission'   => array(),
        ':Resource'     => array(),
        ':Role'         => array(),
    );

    $this->assertEquals(
      $tokens,
      $revoke->getTokens(),
      'A REVOKE should have the following schema: ' . $queryClass::SCHEMA
    );
  }

  public function testConstruct()
  {
    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $query = 'REVOKE ON TO';

    $this->assertEquals($query, $revoke->getRaw());
  }

  public function testRevoke()
  {
    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->revoke('ALL');
    $query = 'REVOKE ALL ON TO';

    $this->assertEquals($query, $revoke->getRaw());

    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->revoke('READ');
    $query = 'REVOKE READ ON TO';

    $this->assertEquals($query, $revoke->getRaw());
  }

  public function testOn()
  {
    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->on('resource');
    $query = 'REVOKE ON resource TO';

    $this->assertEquals($query, $revoke->getRaw());

    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->on('resource2');
    $query = 'REVOKE ON resource2 TO';

    $this->assertEquals($query, $revoke->getRaw());
  }

  public function testTo()
  {
    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->to('user');
    $query = 'REVOKE ON TO user';

    $this->assertEquals($query, $revoke->getRaw());

    $revoke = new \Orient\Query\Command\Credential\Revoke();
    $revoke->to('user2');
    $query = 'REVOKE ON TO user2';

    $this->assertEquals($query, $revoke->getRaw());
  }
}

