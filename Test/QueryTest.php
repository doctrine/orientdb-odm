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

use Orient\Test\PHPUnit\TestCase;
use Orient\Query;

class QueryTest extends TestCase
{ 
  public function setup()
  {
    $this->query = new Query();
  }

  public function testDataFiltering()
  {
    $this->query->where('username = ?', "\"admin\"", false);
    $sql    =
      'SELECT FROM WHERE username = "\"admin\""'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->where('username = ?', "'admin'", false);
    $sql    =
      "SELECT FROM WHERE username = \"\'admin\'\"";
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->insert(array('field'))->values(array('value'))->into('class');
    $sql    =
      'INSERT INTO class () VALUES ("value")'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query = new Query();
    $this->query->where('any() traverse ( any() like "%danger%" )', null, false);
    $sql    =
      'SELECT FROM WHERE any() traverse ( any() like "%danger%" )'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->where('1 = ?', '1; DELETE FROM class', false);
    $sql    =
      'SELECT FROM WHERE 1 = "1; DELETE FROM class"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->where('1 = ?', '1"; DELETE FROM class', false);
    $sql    =
      'SELECT FROM WHERE 1 = "1\"; DELETE FROM class"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->from(array('users; DELETE FROM class'), false)->resetWhere();
    $sql    =
      'SELECT FROM usersDELETEFROMclass'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->from(array('users-- DELETE FROM class'), false)->resetWhere();
    $sql    =
      'SELECT FROM usersDELETEFROMclass'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->from(array('class'), false)->where('class = ?', ";");
    $sql    =
      'SELECT FROM class WHERE class = ";"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->from(array('class'), false)->where('class = ?', "--");
    $sql    =
      'SELECT FROM class WHERE class = "--"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testSelect()
  {
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Select', $this->query->select(array()));
  }

  public function testYouCanResetAllTheWheresOfAQuery()
  {
    $this->query  = new Query(array('myClass'));
    $this->query->where('the sky = ?', 'blue');
    $this->query->resetWhere();
    $sql    =
      'SELECT FROM myClass'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testInsert()
  {
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Insert', $this->query->insert());
  }

  public function testGrant()
  {
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Credential', $this->query->grant('p'));
    $this->assertInstanceOf('\Orient\Query\Command\Credential\Grant', $this->query->grant('p'));
  }

  public function testYouCanCreateARevoke()
  {
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Credential', $this->query->revoke('p'));
    $this->assertInstanceOf('\Orient\Query\Command\Credential\Revoke', $this->query->revoke('p'));
  }

  public function testCreationOfAClass()
  {
    $this->assertInstanceOf('\Orient\Query\Command\OClass\Create', $this->query->create('p'));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\OClass', $this->query->create('p'));
  }

  public function testRemovalOfAClass()
  {
    $this->assertInstanceOf('\Orient\Query\Command\OClass\Drop', $this->query->drop('p'));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\OClass', $this->query->drop('p'));
  }

  public function testRemovalOfAProperty()
  {
    $this->assertInstanceOf('\Orient\Query\Command\Property\Drop', $this->query->drop('p', 'h'));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Property', $this->query->drop('p', 'h'));

    
    $this->query  = new Query();
    $this->query->drop("read", "hallo");
    $sql    =
      'DROP PROPERTY read.hallo'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->drop("run", "forrest")->on('c');
    $sql    =
      'DROP PROPERTY c.forrest'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testCreationOfAProperty()
  {
    $this->assertInstanceOf('\Orient\Query\Command\Property\Create', $this->query->create('p', 'h'));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Property', $this->query->create('p', 'h'));
    
    $this->query->create("read", "hallo", "type");
    $sql    =
      'CREATE PROPERTY read.hallo type'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->create("run", "forrest", "type", "Friend");
    $sql    =
      'CREATE PROPERTY run.forrest type Friend'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testFindReferences()
  {
    $this->assertInstanceOf('\Orient\Query\Command\Reference\Find', $this->query->findReferences("1:1"));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Reference\Find', $this->query->findReferences("1:1"));

    $this->query->in(array('class2', 'cluster:class3'));
    $sql    =
      'FIND REFERENCES 1:1 [class2, cluster:class3]'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testDroppingAnIndex()
  {
    $this->assertInstanceOf('\Orient\Query\Command\Index\Drop', $this->query->unindex("class", "property"));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Index', $this->query->unindex("class", "property"));

    $this->query->unindex("class", "property");
    $sql    =
      'DROP INDEX class.property'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testCreatingAnIndex()
  {
    $this->assertInstanceOf('\Orient\Query\Command\Index\Create', $this->query->index("class", "property"));
    $this->assertInstanceOf('\Orient\Contract\Query\Command\Index', $this->query->index("class", "property"));
    
    $this->query->index("class", "property");
    $sql    =
      'CREATE INDEX class.property'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testDeleteSQLQuery()
  {
    $this->query->delete("Profile")
                ->where("1 = ?", 1)
                ->andWhere("links = ?", 1);
    
    $sql    =
      'DELETE FROM Profile WHERE 1 = "1" AND links = "1"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testCreatingALink()
  {
    $this->query->link('class', "property", "Profile")->to("class2", "property2");

    $sql    =
      'CREATE LINK Profile FROM class.property TO class2.property2'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->link('class', "property", "Profile", true)->to("class2", "property2");

    $sql    =
      'CREATE LINK Profile FROM class.property TO class2.property2 INVERSE'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testUpdating()
  {
    $this->query->update('class')
                ->set(array('first' => 'uno', 'nano' => 'due'))
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class SET first = "uno", nano = "due" WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->add(array('first' => '10:22', 'nano' => '10:1'), 'class')
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class ADD first = 10:22, nano = 10:1 WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->remove(array('first' => 'uno', 'nano' => 'due'), 'class')
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class REMOVE first = "uno", nano = "due" WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }
}
