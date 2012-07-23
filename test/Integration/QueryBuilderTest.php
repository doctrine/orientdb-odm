<?php

/**
 * QueryBuilderTest class
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <daniele.alessandri@gmail.com>
 */

namespace test\Integration;

use test\PHPUnit\TestCase;
use Congow\Orient\Query;
use Congow\Orient\Binding\HttpBinding;
use Congow\Orient\Contract\Binding\HttpBindingResultInterface;

class QueryBuilderTest extends TestCase
{
    public function setup()
    {
        $this->binding = $this->createHttpBinding();
    }

    public function testSelect()
    {
        $query = new Query();

        $this->assertHttpStatus(200, $this->doQuery($query->from(array('address'))));
        $this->assertHttpStatus(200, $this->doQuery($query->select(array('@version', 'street'))));
    }

    public function testSelectRange()
    {
        $query = new Query();

        $this->assertHttpStatus(200, $this->doQuery($query->from(array('Address'))->range('13:0')));
        $this->assertHttpStatus(200, $this->doQuery($query->range(null, '12')));
        $this->assertHttpStatus(200, $this->doQuery($query->range('10.0')));
        $this->assertHttpStatus(200, $this->doQuery($query->range('10.1', false)));
        $this->assertHttpStatus(200, $this->doQuery($query->range('13:0', '13:2')));

        /**
         * @todo what? there should be 2 records:
         * @see http://code.google.com/p/orient/issues/detail?id=574&thanks=574&ts=1318783142
         */
        //$this->assertEquals(165, $this->countResults($this->query()));
    }

    public function testSelectLimit()
    {
        $query = new Query();

        $result = $this->doQuery($query->from(array('Address'))->limit(20));
        $this->assertHttpStatus(200, $result);
        $this->assertSame(20, $this->getResultCount($result));

        $query->from(array('Address'))->limit(30);
        $query->from(array('Address'))->limit(20);

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertSame(20, $this->getResultCount($result));

        $result = $this->doQuery($query->from(array('Address'))->limit('a'));
        $this->assertHttpStatus(200, $result);
        $this->assertGreaterThan(21, $this->getResultCount($result));
    }

    public function testSelectByRID()
    {
        $query = new Query();
        $query->from(array('13:100'));

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid('13:100', $result);
    }

    public function testSelectOrderBy()
    {
        $query = new Query();
        $query->from(array('13:100', '13:101'))
              ->orderBy('rid ASC')
              ->orderBy('street DESC');

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid('13:100', $result);

        $query->orderBy('rid DESC', false);

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid('13:101', $result);
    }

    public function testSelectComplex()
    {
        $query = new Query();
        $query->limit(10)
              ->limit(20)
              ->from(array('13:2', '13:4'), false)
              ->select(array('rid', 'street'))
              ->select(array('type'))
              ->range('13:2')
              ->range(null, '13:4')
              ->orderBy('street ASC');

        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testInsertRecord()
    {
        $binding = $this->createHttpBinding();

        $before = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));

        $query = new Query();
        $query->insert()
              ->fields(array('street', 'type', 'city'))
              ->values(array('5th avenue', 'villetta', '#13:0'))
              ->into('Address');

        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $after = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));
        $this->assertSame($after, $before + 1);
    }

    /**
     * @depends testInsertRecord
     */
    public function testDeleteRecord()
    {
        $binding = $this->createHttpBinding();

        $before = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));

        $query = new Query();
        $query->delete('Address')
              ->where('street = ?', '5th avenue')
              ->orWhere('type = "villetta"');

        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $after = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));
        $this->assertSame($after, $before - 1);
    }

    /**
     * @todo Ugly as hell...
     */
    public function testInsertRecordAgain()
    {
        $this->testInsertRecord();
    }

    /**
     * @depends testInsertRecordAgain
     */
    public function testTruncateRecord()
    {
        $binding = $this->createHttpBinding();
        $before = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));
        $query = new Query();

        $query->from(array('Address'))
              ->where('street = ?', '5th avenue')
              ->orWhere('type = "villetta"');

        $result = $this->doQuery($query, $binding)->getResult();

        $query->truncate(substr($result[0]->{"@rid"}, 1));
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $after = $this->getResultCount($binding->command('SELECT count(*) FROM Address'));
        $this->assertSame($after, $before - 1);
    }

    public function testGrantCredentials()
    {
        $query = new Query();

        $query->grant('READ')
              ->to('reader')
              ->on('Address');

        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testRevokeCredentials()
    {
        $query = new Query();
        $query->revoke('READ')
              ->to('reader')
              ->on('Address');

        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testIndexCreate()
    {
        $binding = $this->createHttpBinding();
        $query = new Query();

        $query->index('index_name_2', 'unique');
        $result = $this->doQuery($query, $binding);
        $this->assertHttpStatus(200, $result);
        $this->assertSame('0', $result->getInnerResponse()->getBody());

        $count = $this->getResultCount($binding->query('SELECT FROM OGraphEdge'));

        $query = new Query();
        $query->index('in', 'unique', 'OGraphEdge');

        $result = $this->doQuery($query, $binding);
        $this->assertHttpStatus(200, $result);
        $this->assertEquals($result->getInnerResponse()->getBody(), $count);
    }

    public function testIndexCount()
    {
        $query = new Query();

        $query->indexCount('index_name_2');
        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testExecutingAIndexLookup()
    {
        $query = new Query();

        $query->lookup('index_name_2');
        $this->assertHttpStatus(200, $this->doQuery($query));

        $query->where('fakekey = ?', 2);
        $this->assertHttpStatus(500, $this->doQuery($query));

        $query = new Query();
        $query->from(array('index:index_name_2'))
              ->between('key', '10.0', '10.1');

        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testAddEntryToIndex()
    {
        $query = new Query();

        $query->indexCount('index_name_2');
        $before = $this->getResultCount($this->doQuery($query));

        $query->indexPut('index_name_2', 'k', '13:100');
        $this->assertHttpStatus(204, $this->doQuery($query));

        $query->indexCount('index_name_2');
        $after = $this->getResultCount($this->doQuery($query));
        $this->assertEquals($after, $before + 1);
    }

    public function testRemoveEntryFromIndex()
    {
        $query = new Query();

        $query->indexCount('index_name_2');
        $before = $this->getResultCount($this->doQuery($query));

        $query->indexRemove('index_name_2', 'k');
        $this->assertHttpStatus(200, $this->doQuery($query));

        $query->indexCount('index_name_2');
        $after = $this->getResultCount($this->doQuery($query));
        $this->assertEquals($after, $before - 1);
    }

    public function tetIndexDrop()
    {
        $query = new Query();

        $query->indexCount('index_name_2');
        $this->assertHttpStatus(200, $this->doQuery($query));

        $query->unindex('index_name_2');
        $this->assertHttpStatus(204, $this->doQuery($query));

        $query->indexCount('index_name_2');
        $this->assertHttpStatus(500, $this->doQuery($query));

        $query->unindex('in', 'OGraphEdge');
        $this->assertHttpStatus(204, $this->doQuery($query));
    }

    public function testFindReferences()
    {
        $query = new Query();

        $query->findReferences('13:0');
        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testClassCreate()
    {
        $class = 'MyCustomTestClass' . microtime();
        $query = new Query();

        $query->create($class);
        $this->assertHttpStatus(200, $this->doQuery($query));

        return $class;
    }

    /**
     * @depends testClassCreate
     */
    public function testTruncateClass($class)
    {
        $query = new Query();
        $query->truncate($class);

        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    /**
     * @depends testClassCreate
     */
    public function testTruncateCluster($class)
    {
        $query = new Query();

        $query->truncate($class, true);
        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    /**
     * @depends testClassCreate
     */
    public function testAlterClass($class)
    {
        $query = new Query();

        $query->alter($class, 'SUPERCLASS', 'OUser');
        $this->assertHttpStatus(204, $this->doQuery($query));

        return $class;
    }

    /**
     * @depends testAlterClass
     */
    public function testCreateProperty($class)
    {
        $query = new Query();

        $query->create($class, 'customTestProperty', 'string');
        $this->assertHttpStatus(200, $this->doQuery($query));

        return $class;
    }

    /**
     * @depends testCreateProperty
     */
    public function testAlterProperty($class)
    {
        $query = new Query();

        $query->alterProperty($class, 'customTestProperty', 'notnull', 'false');
        $this->assertHttpStatus(204, $this->doQuery($query));

        $query->alterProperty($class, 'customTestProperty', 'notnull', 'true');
        $this->assertHttpStatus(204, $this->doQuery($query));

        return $class;
    }

    /**
     * @depends testAlterProperty
     */
    public function testDropProperty($class)
    {
        $query = new Query();

        $query->drop($class, 'customTestProperty');
        $this->assertHttpStatus(204, $this->doQuery($query));

        return $class;
    }

    /**
     * @depends testAlterClass
     */
    public function testDropClass($class)
    {
        $query = new Query();

        $query->drop($class);
        $this->assertHttpStatus(204, $this->doQuery($query));

        return $class;
    }

    public function testLinkObjects()
    {
        $query = new Query();

        $query->link('Company', 'id', 'in', true)->with('ORole', 'id');
        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testUpdate()
    {
        $query = new Query();
        $binding = $this->createHttpBinding();

        $query->update('Address')->set(array('nick' => 'Luca'))
              ->orWhere('@rid = ?', '13:101');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM Address WHERE @rid = #13:101')->getResult();
        $this->assertSame('Luca', $records[0]->nick);

        $query->update('Address')->set(array('nick' => 'Luca2'))
              ->orWhere('@rid = ?', '13:101');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM Address WHERE @rid = #13:101')->getResult();
        $this->assertSame('Luca2', $records[0]->nick);
    }

    public function testAddLink()
    {
        $query = new Query();
        $binding = $this->createHttpBinding();

        $records = $binding->command('SELECT FROM 30:1')->getResult();
        $before = count($records[0]->comments);

        $query->add(array('comments' => '31:0'), 'post')
              ->where('@rid = ?', '30:1');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM 30:1')->getResult();
        $after = count($records[0]->comments);

        $this->assertSame($after, $before + 1);
    }

    /**
     * @depends testAddLink
     */
    public function testRemoveLink()
    {
        $query = new Query();
        $binding = $this->createHttpBinding();

        $records = $binding->command('SELECT FROM 30:1')->getResult();
        $before = count($records[0]->comments);

        $query->remove(array('comments' => '31:0'), 'post')
              ->where('@rid = ?', '30:1');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM 30:1')->getResult();
        $after = count($records[0]->comments);

        $this->assertSame($after, $before - 1);
    }

    public function testPutLink()
    {
        $query = new Query();
        $binding = $this->createHttpBinding();

        $binding->command('UPDATE profile REMOVE followers');

        $query->put(array('followers' => array('Johnny' => '10:2')), 'profile')
              ->where('@rid = ?', '10:1');

        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM 10:1')->getResult();
        $this->assertInstanceOf('\stdClass', $records[0]->followers);
        $this->assertSame('#10:2', $records[0]->followers->Johnny);
    }

    public function testTruncateNonExistingClass()
    {
        $query = new Query();

        $query->truncate('NON_EXISTING_CLASS');
        $this->assertHttpStatus(500, $this->doQuery($query));
    }

    public function testTruncateNonExistingCluster()
    {
        $query = new Query();

        $query->truncate('NON_EXISTING_CLUSTER', true);
        $this->assertHttpStatus(500, $this->doQuery($query));
    }

    protected function doQuery($query, HttpBinding $binding = null)
    {
        $binding = $binding ?: $this->binding;
        $result = $binding->command($query->getRaw());

        return $result;
    }

    protected function assertFirstRid($rid, HttpBindingResultInterface $result)
    {
        $records = $result->getResult();
        $this->assertSame("#$rid", $records[0]->{'@rid'}, "The first RID of the results is $rid");
    }

    protected function getResultCount(HttpBindingResultInterface $result)
    {
        $response = json_decode($result->getInnerResponse()->getBody());

        if (array_key_exists(0, $response->result) && property_exists($response->result[0], 'count')) {
            return $response->result[0]->count;
        }

        if (array_key_exists(0, $response->result) && property_exists($response->result[0], 'size')) {
            return $response->result[0]->size;
        }

        if (property_exists($response, 'result')) {
            return count($response->result);
        }

        throw new \Exception('Unable to retrieve a count from the given response.');
    }
}
