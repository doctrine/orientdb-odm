<?php

/**
 * RevokeTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command\Credential;

use Orient\Query\Command\Credential\Revoke;
use Orient\Test\PHPUnit\TestCase;

class RevokeTest extends TestCase
{
  public function setUp()
  {
    $this->revoke = new Revoke('myPermission');
  }

  public function testRevokeHasSomeKnownTokens()
  {
    $tokens       = array(
        ':Permission'   => array(),
        ':Resource'     => array(),
        ':Role'         => array(),
    );

    $this->assertTokens($tokens, $this->revoke->getTokens());
  }

  public function testSynthaxIsRightAfterObjectCreation()
  {
    $query = 'REVOKE myPermission ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());
  }

  public function testRevokeCommandWorksAndCanBeOverwritten()
  {
    $query = 'REVOKE myPermission ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());

    $this->revoke->permission('READ');
    $query = 'REVOKE READ ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());
  }

  public function testUsingTheFluentInterface()
  {
    $this->revoke->permission("read")
          ->to("myUser")
          ->to("myOtherUser")
          ->on("server");
    $sql    =
      'REVOKE read ON server TO myOtherUser'
    ;

    $this->assertEquals($sql, $this->revoke->getRaw());
  }
}

