<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

use Orient\Query\Command;

class MockCommand extends Command
{
  const SCHEMA = ":Target :Where";

  public function  __construct(array $target = NULL)
  {
    $this->statement  = self::SCHEMA;
    $this->tokens     = $this->getTokens();
  }
}

class CommandTest extends PHPUnit_Framework_TestCase
{
  public function testFrom()
  {
    $command  = new MockCommand();
    $from     = array('Cities');
    $command->from($from);

    $this->assertEquals($from, $command->getTokenValue('Target'));
  }

  public function testGetRaw()
  {
    $command  = new MockCommand();
    $from     = array('Cities');
    $command->from($from);

    $this->assertEquals("Cities", $command->getRaw());
  }

  public function testGetTokens()
  {
    $this->assertEquals(array(':Target' => array(), ':Where' => array()), MockCommand::getTokens());
  }

  public function testResetWhere()
  {
    $command  = new MockCommand();
    $from     = array('Cities');
    $command->where("i loves ?", "U");
    $command->resetWhere();

    $this->assertEquals(array(), $command->getTokenValue('Where'));
  }

  public function testWhere()
  {
    $command  = new MockCommand();
    $from     = array('Cities');
    $command->where("i loves ?", "U");
    $command->where("mark loves ?", "me", true, "OR");

    $this->assertEquals("WHERE i loves \"U\" OR mark loves \"me\"", $command->getRaw());
  }

  /**
   * @expectedException Orient\Exception\Query\Command\TokenNotFound
   */
  public function testCheckAnExceptionRaisedWhenRequestingInvalidToken()
  {
    $command  = new MockCommand();
    $command->getTokenValue('buffalo');
  }
}
