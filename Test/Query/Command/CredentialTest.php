<?php

/**
 * RevokeTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test\Query\Command;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query\Command\Credential;

class CredentialStub extends Credential
{
  const SCHEMA = "STUB :Permission ON :Resource TO :Role";
}

class CredentialTest extends TestCase
{
  public function setUp()
  {
    $this->credential = new CredentialStub('p');
  }

  public function testOnCommandWorksAndCanBeOverwritten()
  {
    $this->credential->on('resource');
    $query = 'STUB p ON resource TO';

    $this->assertCommandGives($query, $this->credential->getRaw());

    $this->credential->on('resource2');
    $query = 'STUB p ON resource2 TO';

    $this->assertCommandGives($query, $this->credential->getRaw());
  }

  public function testToCommandWorksAndCanBeOverwritten()
  {
    $this->credential->to('user');
    $query = 'STUB p ON TO user';

    $this->assertCommandGives($query, $this->credential->getRaw());

    $this->credential->to('user2');
    $query = 'STUB p ON TO user2';

    $this->assertCommandGives($query, $this->credential->getRaw());
  }
}

