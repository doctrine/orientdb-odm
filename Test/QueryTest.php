<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test;

use Orient\Query\Command\Select;
use Orient\Query\Command\Insert;
use Orient\Query\Command\Credential\Grant;
use Orient\Query\Command\Credential\Revoke;
use Orient\Test\PHPUnit\TestCase;
use Orient\Query;

class QueryTest extends TestCase
{ 
  public function setup()
  {
    $this->query = new Query($this->getCommands());
  }

  public static function getCommands()
  {
    return array(
        'select'  => new Select(),
        'insert'  => new Insert(),
        'grant'   => new Grant(),
        'revoke'  => new Revoke()
    );
  }
  
  public function testTheQueryTokensAreValid()
  {
    $this->assertTokens(Select::getTokens(), $this->query->getTokens());
    $this->assertTokens(Insert::getTokens(), $this->query->insert()->getTokens());
    $this->assertTokens(Grant::getTokens(), $this->query->grant('what')->getTokens());
    $this->assertTokens(Revoke::getTokens(), $this->query->revoke('what')->getTokens());
  }

  public function testYouCanCreateASelect()
  {
    $this->query  = new Query(array('select' => new Select(array('myClass'))));
    $this->query->select(array('name', 'username', 'email'))
          ->from(array('12:0', '12:1'), false)
          ->where('any() traverse ( any() like "%danger%" )')
          ->orWhere("1 = ?", 1)
          ->andWhere("links = ?", 1)
          ->limit(20)
          ->orderBy('username')
          ->orderBy('name', true, true)
          ->range("12:0", "12:1");
    $sql    =
      'SELECT name, username, email FROM [12:0, 12:1] WHERE any() traverse ( any() like "%danger%" ) OR 1 = "1" AND links = "1" ORDER BY name, username LIMIT 20 RANGE 12:0, 12:1'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testYouCanResetAllTheWheresOfAQuery()
  {
    $this->query  = new Query(array('select' => new Select(array('myClass'))));
    $this->query->where('the sky = ?', 'blue');
    $sql    =
      'SELECT FROM myClass WHERE the sky = "blue"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->resetWhere();
    $sql    =
      'SELECT FROM myClass'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query  = new Query(array('select' => new Select(array('myClass'))));
    $this->query->where('the sky = ?', 'blue');
    $sql    =
      'SELECT FROM myClass WHERE the sky = "blue"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testYouCanCreateAnInsert()
  {
    $this->query  = new Query($this->getCommands());
    $this->query->insert()
          ->into("myClass")
          ->fields(array('name', 'relation', 'links'))
          ->values(array(
            'hello', array('10:1'), array('10:1', '11:1')
          ));
    $sql    =
      'INSERT INTO myClass (name, relation, links) VALUES ("hello", 10:1, [10:1, 11:1])'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testYouCanCreateAGrant()
  {
    $this->query  = new Query($this->getCommands());
    $this->query->grant("read")
          ->to("myUser")
          ->to("myOtherUser")
          ->on("server");
    $sql    =
      'GRANT read ON server TO myOtherUser'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testYouCanCreateARevoke()
  {
    $this->query  = new Query($this->getCommands());
    $this->query->revoke("read")
          ->to("myUser")
          ->to("myOtherUser")
          ->on("server");
    $sql    =
      'REVOKE read ON server TO myOtherUser'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  /**
   * @expectedException Orient\Exception
   */
  public function testWithoutInjectingCommandAnExceptionIsRaised()
  {
    $this->query  = new Query(array());
  }
}
