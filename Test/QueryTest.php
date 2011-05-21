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
use Orient\Query\Command\Delete;
use Orient\Query\Command\Credential\Grant;
use Orient\Query\Command\Credential\Revoke;
use Orient\Query\Command\OClass\Create;
use Orient\Query\Command\OClass\Drop;
use Orient\Query\Command\Reference\Find;
use Orient\Query\Command\Property\Drop as DropProperty;
use Orient\Query\Command\Index\Drop as DropIndex;
use Orient\Query\Command\Index\Create as CreateIndex;
use Orient\Query\Command\Property\Create as CreateProperty;
use Orient\Test\PHPUnit\TestCase;
use Orient\Query;

class QueryTest extends TestCase
{ 
  public function setup()
  {
    $this->query = new Query();
  }
  
  public function testTheQueryTokensAreValid()
  {
    $this->assertTokens(Select::getTokens(), $this->query->getTokens());
    $this->assertTokens(Insert::getTokens(), $this->query->insert()->getTokens());
    $this->assertTokens(Grant::getTokens(), $this->query->grant('what')->getTokens());
    $this->assertTokens(Revoke::getTokens(), $this->query->revoke('what')->getTokens());
    $this->assertTokens(Create::getTokens(), $this->query->create('what')->getTokens());
    $this->assertTokens(Drop::getTokens(), $this->query->drop('what')->getTokens());
    $this->assertTokens(DropProperty::getTokens(), $this->query->drop('what', 'hallo')->getTokens());
    $this->assertTokens(CreateProperty::getTokens(), $this->query->create('what', 'hallo', "a")->getTokens());
    $this->assertTokens(CreateIndex::getTokens(), $this->query->index('what', 'hallo')->getTokens());
    $this->assertTokens(DropIndex::getTokens(), $this->query->unindex('what', 'hallo')->getTokens());
  }

  public function testYouCanCreateASelect()
  {
    $this->query  = new Query();
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
    $this->query  = new Query(array('myClass'));
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

    $this->query  = new Query(array('myClass'));
    $this->query->where('the sky = ?', 'blue');
    $sql    =
      'SELECT FROM myClass WHERE the sky = "blue"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }

  public function testYouCanCreateAnInsert()
  {
    $this->query  = new Query();
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
    $this->query  = new Query();
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
    $this->query  = new Query();
    $this->query->revoke("read")
          ->to("myUser")
          ->to("myOtherUser")
          ->on("server");
    $sql    =
      'REVOKE read ON server TO myOtherUser'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testCreationOfAClass()
  {
    $this->query  = new Query();
    $this->query->create("read");
    $sql    =
      'CREATE CLASS read'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testRemovalOfAClass()
  {
    $this->query  = new Query();
    $this->query->drop("read");
    $sql    =
      'DROP CLASS read'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testRemovalOfAProperty()
  {
    $this->query  = new Query();
    $this->query->drop("read", "hallo");
    $sql    =
      'DROP PROPERTY read.hallo'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->drop("run", "forrest");
    $sql    =
      'DROP PROPERTY run.forrest'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testCreationOfAProperty()
  {
    $this->query  = new Query();
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
    $this->query  = new Query();
    $this->query->findReferences("1:1");
    $sql    =
      'FIND REFERENCES 1:1'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->findReferences("1:2", array('class'));
    $sql    =
      'FIND REFERENCES 1:2 [class]'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->in(array('class2', 'cluster:class3'));
    $sql    =
      'FIND REFERENCES 1:2 [class, class2, cluster:class3]'
    ;

    $this->assertEquals($sql, $this->query->getRaw());

    $this->query->findReferences("1:3", array('class'), false);
    $sql    =
      'FIND REFERENCES 1:3 [class]'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testDroppingAnIndex()
  {
    $this->query  = new Query();
    $this->query->unindex("class", "property");
    $sql    =
      'DROP INDEX class.property'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testCreatingAnIndex()
  {
    $this->query  = new Query();
    $this->query->index("class", "property");
    $sql    =
      'CREATE INDEX class.property'
    ;

    $this->assertEquals($sql, $this->query->getRaw());
  }

  public function testDeleteSQLQuery()
  {
    $this->query  = new Query();
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

    $this->query->add(array('first' => '10:22', 'nano' => array('10:1', '10:2')), 'class')
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class ADD first = 10:22, nano = [10:1, 10:2] WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->remove(array('first' => 'uno', 'nano' => 'due'), 'class')
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class REMOVE first = "uno", nano = "due" WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());

    $this->query->put(array('first' => array('key' => 'value'), 'nano' => 'due'), 'class')
                ->where('prop = ?', 'val');

    $sql    =
      'UPDATE class PUT first = "key", value, nano = "due" WHERE prop = "val"'
    ;

    $this->assertCommandGives($sql, $this->query->getRaw());
  }
}
