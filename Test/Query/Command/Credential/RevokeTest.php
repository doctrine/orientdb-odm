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
    $this->revoke = new Revoke();
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
    $query = 'REVOKE ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());
  }

  public function testRevokeCommandWorksAndCanBeOverwritten()
  {
    $this->revoke->revoke('ALL');
    $query = 'REVOKE ALL ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());

    $this->revoke->revoke('READ');
    $query = 'REVOKE READ ON TO';

    $this->assertCommandGives($query, $this->revoke->getRaw());
  }
}

