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

use Orient\Query\Command\Credential\Grant;
use Orient\Test\PHPUnit\TestCase;

class GrantTest extends TestCase
{
  public function setup()
  {
    $this->grant  = new Grant();
  }

  public function testSchema()
  {
    $tokens       = array(
        ':Permission'   => array(),
        ':Resource'     => array(),
        ':Role'         => array(),
    );

    $this->assertTokens($tokens, $this->grant->getTokens());
  }

  public function testSynthaxIsRightAfterConstruction()
  {
    $query = 'GRANT ON TO';

    $this->assertCommandGives($query, $this->grant->getRaw());
  }

  public function testGrantCommandWorksAndCanBeOverWritten()
  {
    $this->grant->grant('ALL');
    $query = 'GRANT ALL ON TO';

    $this->assertCommandGives($query, $this->grant->getRaw());

    $this->grant->grant('READ');
    $query = 'GRANT READ ON TO';

    $this->assertCommandGives($query, $this->grant->getRaw());
  }
}

