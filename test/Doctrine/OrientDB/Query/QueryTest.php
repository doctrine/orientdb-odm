<?php

/**
 * QueryTest
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace test\Doctrine\OrientDB\Query;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Query\Command\Insert;
use Doctrine\OrientDB\Query\Command\Select;
use Doctrine\OrientDB\Query\Command\Update;

class QueryTest extends TestCase
{
    public function setup()
    {
        $this->query = new Query();
    }

    public function testQueryImplementsAGenericInterface()
    {
        $this->assertInstanceOf("Doctrine\OrientDB\Query\QueryInterface", $this->query);
    }

    public function testDataFiltering()
    {
        $this->query->where('username = ?', "\"admin\"", false);
        $sql = 'SELECT FROM WHERE username = "\"admin\""';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->where('username = ?', "'admin'", false);
        $sql = "SELECT FROM WHERE username = \"\'admin\'\"";

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->insert(array('field'))->values(array('value'))->into('class');
        $sql = 'INSERT INTO class () VALUES ("value")';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query = new Query();
        $this->query->where('any() traverse ( any() like "%danger%" )', null, false);
        $sql = 'SELECT FROM WHERE any() traverse ( any() like "%danger%" )';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->where('1 = ?', '1; DELETE FROM class', false);
        $sql = 'SELECT FROM WHERE 1 = "1; DELETE FROM class"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->where('1 = ?', '1"; DELETE FROM class', false);
        $sql = 'SELECT FROM WHERE 1 = "1\"; DELETE FROM class"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->from(array('users; DELETE FROM class'), false)->resetWhere();
        $sql = 'SELECT FROM usersDELETEFROMclass';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->from(array('users-- DELETE FROM class'), false)->resetWhere();
        $sql = 'SELECT FROM usersDELETEFROMclass';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->from(array('class'), false)->where('class = ?', ";");
        $sql = 'SELECT FROM class WHERE class = ";"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->from(array('class'), false)->where('class = ?', "--");
        $sql = 'SELECT FROM class WHERE class = "--"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->select(array('count(*)'))->from(array('class'), false);
        $this->query->resetWhere();
        $sql = 'SELECT count(*) FROM class';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testSelect()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Select', $this->query->select(array()));
    }

    public function testYouCanResetAllTheWheresOfAQuery()
    {
        $this->query = new Query(array('myClass'));
        $this->query->where('the sky = ?', 'blue');
        $this->query->resetWhere();
        $sql = 'SELECT FROM myClass';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testInsert()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\InsertInterface', $this->query->insert());
    }

    public function testFields()
    {
        $this->query->insert();
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\InsertInterface', $this->query->fields(array()));
    }

    public function testInto()
    {
        $this->query->insert();
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\InsertInterface', $this->query->into('class'));
    }

    public function testValues()
    {
        $this->query->insert();
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\InsertInterface', $this->query->values(array()));
    }

    public function testTo()
    {
        $this->query->grant('a');
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Credential\Grant', $this->query->to('c'));
    }

    public function testGrant()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\CredentialInterface', $this->query->grant('p'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Credential\Grant', $this->query->grant('p'));
    }

    public function testYouCanCreateARevoke()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\CredentialInterface', $this->query->revoke('p'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Credential\Revoke', $this->query->revoke('p'));
    }

    public function testCreationOfAClass()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\OClass\Create', $this->query->create('p'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\OClassInterface', $this->query->create('p'));
    }

    public function testRemovalOfAClass()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\OClass\Drop', $this->query->drop('p'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\OClassInterface', $this->query->drop('p'));
    }

    public function testRemovalOfAProperty()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Drop', $this->query->drop('p', 'h'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property', $this->query->drop('p', 'h'));


        $this->query = new Query();
        $this->query->drop("read", "hallo");
        $sql = 'DROP PROPERTY read.hallo';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->drop("run", "forrest")->on('c');
        $sql = 'DROP PROPERTY c.forrest';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testCreationOfAProperty()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Create', $this->query->create('p', 'h'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property', $this->query->create('p', 'h'));

        $this->query->create("read", "hallo", "type");
        $sql = 'CREATE PROPERTY read.hallo type';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->create("run", "forrest", "type", "Friend");
        $sql = 'CREATE PROPERTY run.forrest type Friend';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testFindReferences()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Reference\Find', $this->query->findReferences("1:1"));
        //$this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Reference\FindInterface', $this->query->findReferences("1:1"));

        $this->query->in(array('class2', 'cluster:class3'));
        $sql = 'FIND REFERENCES 1:1 [class2, cluster:class3]';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testDroppingAnIndex()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Drop', $this->query->unindex("class", "property"));
        //$this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\IndexInterface', $this->query->unindex("class", "property"));

        $this->query->unindex("property", "class");
        $sql = 'DROP INDEX class.property';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->unindex("property");
        $sql = 'DROP INDEX property';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testLookingUpAnIndex()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Lookup', $this->query->lookup('index'));
    }

    public function testCreatingAnIndex()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Create', $this->query->index("class", "property"));
        //$this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\IndexInterface', $this->query->index("class", "property"));

        $this->query->index("property", 'unique',"class");
        $sql = 'CREATE INDEX class.property unique';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->index("property", 'unique')->type('string');
        $sql = 'CREATE INDEX property string';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->index("property", 'unique');
        $this->query->type('string');
        $sql = 'CREATE INDEX property string';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->index("property", 'string');
        $sql = 'CREATE INDEX property string';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testRebuildAnIndex()
    {
        $this->query->rebuild('Profile.nick');
        $sql = 'REBUILD INDEX Profile.nick';

        $this->assertEquals($sql, $this->query->getRaw());

        $this->query->rebuild('*');
        $sql = 'REBUILD INDEX *';

        $this->assertEquals($sql, $this->query->getRaw());
    }

    public function testDeleteSQLQuery()
    {
        $this->query
            ->delete("Profile")
            ->where("1 = ?", 1)
            ->andWhere("links = ?", "1");

        $sql = 'DELETE FROM Profile WHERE 1 = 1 AND links = "1"';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testDeletingEntriesFromAnIndex()
    {
        $this->query->delete("index:indexName");
        $sql = 'DELETE FROM index:indexName';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testCreatingALink()
    {
        $this->query->link('class', "property", "Profile")->with("class2", "property2");
        $sql = 'CREATE LINK Profile FROM class.property TO class2.property2';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query->link('class', "property", "Profile", true)->with("class2", "property2");
        $sql = 'CREATE LINK Profile FROM class.property TO class2.property2 INVERSE';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testUpdating()
    {
        $this->query
            ->update('class')
            ->set(array('first' => 'uno', 'nano' => 'due', 'linklist' => array(1, 2, 3)))
            ->where('prop = ?', 'val');

        $sql = 'UPDATE class SET first = "uno", nano = "due", linklist = [1,2,3] WHERE prop = "val"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query
            ->put(array('first' => array('1' => '12:0'), 'second' => array('2' => '13:0')), 'puttedClass');

        $sql = 'UPDATE puttedClass PUT first = \'1\', #12:0, second = \'2\', #13:0';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query
                ->add(array('first' => '10:22', 'nano' => '10:1'), 'class')
                ->where('prop = ?', 'val');

        $sql = 'UPDATE class ADD first = #10:22, nano = #10:1 WHERE prop = "val"';

        $this->assertCommandGives($sql, $this->query->getRaw());

        $this->query
                ->remove(array('first' => '12:0', 'nano' => '12:2'), 'class')
                ->where('prop = ?', 'val');

        $sql = 'UPDATE class REMOVE first = #12:0, nano = #12:2 WHERE prop = "val"';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testWheres()
    {
        $this->query->from(array('class'));
        $this->query->where('1 = ?', 1);
        $this->query->andWhere('1 = ?', "1");
        $this->query->orWhere("1 = ?", false);

        $sql = 'SELECT FROM class WHERE 1 = 1 AND 1 = "1" OR 1 = FALSE';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testYouCanJustUseAndWhere()
    {
        $this->query->from(array('class'));
        $this->query->andWhere('1 = ?', 1);
        $this->query->andWhere('1 = ?', "1");
        $this->query->orWhere("1 = ?", false);

        $sql = 'SELECT FROM class WHERE 1 = 1 AND 1 = "1" OR 1 = FALSE';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testLimit()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\SelectInterface', $this->query->limit(20));

        $this->query->from(array('class'))->limit(10);
        $sql = 'SELECT FROM class LIMIT 10';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testSkip()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\SelectInterface', $this->query->skip(10));

        $this->query->from(array('class'))->skip(10);
        $sql = 'SELECT FROM class SKIP 10';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testOn()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Create', $this->query->create('p', 'c'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Create', $this->query->on('p', 'f'));

        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Drop', $this->query->drop('p', 'c'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Drop', $this->query->on('c', 'c'));

        $sql = 'DROP PROPERTY c.c';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testAlter()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\OClass\Alter', $this->query->alter('c', 'p', 'v'));

        $sql = 'ALTER CLASS c p v';

        $this->assertCommandGives($sql, $this->query->alter('c', 'p', 'v')->getRaw());
    }

    public function testAlterProperty()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Property\Alter', $this->query->alterProperty('p', 'c', 'a', 'v'));

        $sql = 'ALTER PROPERTY c.p a v';

        $this->assertCommandGives($sql, $this->query->alterProperty('c', 'p', 'a', 'v')->getRaw());
    }

    public function testOrderBy()
    {
        $this->query->from(array('class'));
        $this->query->orderBy('A B');
        $sql = 'SELECT FROM class ORDER BY A B';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testPuttingANewIndex()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Put', $this->query->indexPut('i', 'k', 'v'));
    }

    public function testANormalSelectCanBeConvertedIntoAnIndexSelect()
    {
        $this->query->from(array('index:name'));
        $this->query->between("k", "10.1", "10.2");
        $sql = 'SELECT FROM index:name WHERE k BETWEEN 10.1 AND 10.2';

        $this->assertCommandGives($sql, $this->query->getRaw());
    }

    public function testAnIndexCanBeRemoved()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Remove', $this->query->indexRemove('i', 'k', 'v'));
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Remove', $this->query->indexRemove('i', 'k'));
    }

    public function testYouCanCountInAnIndex()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Index\Count', $this->query->indexCount('i'));
    }

    /**
     * @expectedException Doctrine\OrientDB\Exception
     */
    public function testAnExceptionIsRaisedWhenTryingToAccessANonExistingMethod()
    {
        $query = $this->getMock('Doctrine\OrientDB\Query\Query', array('aMethodCallingABadCommand'));
        $getCommandClass = new \ReflectionMethod($query, 'getCommandClass');
        $getCommandClass->setAccessible(true);

        $aMethodCallingABadCommand = function () use ($query, $getCommandClass) {
            $getCommandClass->invoke($query, 'OMN NO NO ON ON');
        };

        $query->expects($this->once())
              ->method('aMethodCallingABadCommand')
              ->will($this->returnCallback($aMethodCallingABadCommand));

        $query->aMethodCallingABadCommand();
    }

    public function testTokens()
    {
        $select = new Select();
        $this->assertCommandGives($select->getTokens(), $this->query->getTokens());
    }

    public function testTruncatingAClass()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Truncate\OClass', $this->query->truncate('myFictionaryClass'));
    }

    public function testTruncatingACluster()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Truncate\Cluster', $this->query->truncate('myFictionaryCluster', true));
    }

    public function testTruncatingARecord()
    {
        $this->assertInstanceOf('\Doctrine\OrientDB\Query\Command\Truncate\Record', $this->query->truncate('12:0'));
    }
}
