<?php

/**
 * QueryBuilderTest class
 *
 * @package    Doctrine\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     Daniele Alessandri <daniele.alessandri@gmail.com>
 */

namespace test\Doctrine\OrientDB\Integration;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Binding\HttpBinding;
use Doctrine\OrientDB\Binding\HttpBindingResultInterface;

/**
 * @group integration
 */
class QueryBuilderTest extends TestCase
{
    public $profile_id;
    public $address_id;
    public $post_id;
    public $comment_id;

    public $binding;

    public function setup()
    {
        $this->binding = $this->createHttpBinding();
        $this->profile_id = $this->binding->getClass('Profile')->getData()->clusters[0];
        $this->address_id = $this->binding->getClass('Address')->getData()->clusters[0];
        $this->post_id = $this->binding->getClass('Post')->getData()->clusters[0];
        $this->comment_id = $this->binding->getClass('Comment')->getData()->clusters[0];
    }

    public function testSelect()
    {
        $query = new Query();

        $this->assertHttpStatus(200, $this->doQuery($query->from(array('address'))));
        $this->assertHttpStatus(200, $this->doQuery($query->select(array('@version', 'street'))));
    }

    public function testSelectBetween()
    {
        $query = new Query();

        $this->assertHttpStatus(200, $this->doQuery($query->from(array('Profile'))->between('@rid', '#'.$this->profile_id.':0', '#'.$this->profile_id.':5')));
    }

    public function testSelectLimit()
    {
        $query = new Query();

        $result = $this->doQuery($query->from(array('Address'))->limit(20));
        $this->assertHttpStatus(200, $result);
        $this->assertSame(20, $this->getResultCount($result));

        $query = new Query();
        $query->from(array('Address'))->limit(30);
        $query->limit(20);

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertSame(20, $this->getResultCount($result));

        $query = new Query();
        $result = $this->doQuery($query->from(array('Address'))->limit('a'));

        $this->assertHttpStatus(200, $result);
        $this->assertGreaterThan(21, $this->getResultCount($result));
    }

    public function testSelectByRID()
    {
        $query = new Query();
        $query->from(array($this->profile_id.':1'));

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid($this->profile_id.':1', $result);
    }

    public function testSelectOrderBy()
    {
        $query = new Query();
        $query->from(array($this->profile_id.':0', $this->profile_id.':1'))
              ->orderBy('@rid ASC')
              ->orderBy('street DESC');

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid($this->profile_id.':0', $result);

        $query->orderBy('@rid DESC', false);

        $result = $this->doQuery($query);
        $this->assertHttpStatus(200, $result);
        $this->assertFirstRid($this->profile_id.':1', $result);
    }

    public function testSelectComplex()
    {
        $query = new Query();
        $query->limit(10)
              ->limit(20)
              ->from(array('19:2', '19:4'), false)
              ->select(array('rid', 'street'))
              ->select(array('type'))
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
              ->values(array('5th avenue', 'villetta', '#18:0'))
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
        $body = $result->getData()->result[0];

        $this->assertSame(0, $body->value);

        $count = $this->getResultCount($binding->query('SELECT FROM Profile'));

        $query = new Query();
        $query->index('name', 'unique', 'Profile');

        $result = $this->doQuery($query, $binding);

        $this->assertHttpStatus(200, $result);
        $value = $result->getData()->result[0]->value;
        $this->assertEquals($value, $count);

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
        $jsonResult = json_decode($this->doQuery($query)->getInnerResponse()->getBody(),true);
        $this->assertHttpStatus(200, $this->doQuery($query));
        $this->assertCount(0,$jsonResult['result']);

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

        $query->indexPut('index_name_2', 'k', '19:100');
        $this->assertHttpStatus(200, $this->doQuery($query));
        //$this->markTestIncomplete('in the previous test the status code whas 204 but now return 200');

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

    public function testIndexDrop()
    {
        $query = new Query();

        $query->indexCount('index_name_2');
        $this->assertHttpStatus(200, $this->doQuery($query));

        $query->unindex('index_name_2');
        $this->assertHttpStatus(200, $this->doQuery($query));

        $query->indexCount('index_name_2');
        $this->assertHttpStatus(500, $this->doQuery($query));

        $query->unindex('in', 'OGraphEdge');
        $this->assertHttpStatus(200, $this->doQuery($query));
    }

    public function testRebuildIndex()
    {
        $query = new Query();
        $query->rebuild('*');

        $this->assertHttpStatus(200,$this->doQuery($query));
    }

    public function testFindReferences()
    {
        $query = new Query();

        $query->findReferences('19:0');
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
        $response = $this->doQuery($query)->getInnerResponse();
        $json = json_decode($response->getBody(),true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($json['result'][0]['value']);


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

        $viaRossi = 'Via Rossi';
        $query->update('Address')->set(array('street' => $viaRossi))
              ->orWhere('@rid = ?', $this->address_id.':3');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM Address WHERE @rid = #'.$this->address_id.':3')->getResult();
        $this->assertSame($viaRossi, $records[0]->street);

        $viaVerdi = "Via Verdi";
        $query->update('Address')->set(array('street' => $viaVerdi))
              ->orWhere('@rid = ?', $this->address_id.':3');
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM Address WHERE @rid = #'.$this->address_id.':3')->getResult();
        $this->assertSame($viaVerdi, $records[0]->street);
    }

    public function testAddLink()
    {
        $query = new Query();
        $binding = $this->createHttpBinding();
        $postRid = $this->post_id . ':0';
        $commentRid = $this->comment_id . ':1';

        $records = $binding->command('SELECT FROM '. $postRid)->getResult();
        $before = count($records[0]->comments);

        $query->add(array('comments' => $commentRid), 'post')
              ->where('@rid = ?', $postRid);
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM ' . $postRid)->getResult();
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
        $postRid = $this->post_id . ':0';
        $commentRid = $this->comment_id . ':1';

        $records = $binding->command('SELECT FROM '.$postRid)->getResult();
        $before = count($records[0]->comments);

        $query->remove(array('comments' => $commentRid), 'post')
              ->where('@rid = ?', $postRid);
        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM '.$postRid)->getResult();
        $after = count($records[0]->comments);

        $this->assertSame($after, $before - 1);
    }

    public function testPutLink()
    {
        $query         = new Query();
        $binding       = $this->createHttpBinding();
        $davidProfile  = '#'. $this->profile_id.':0';
        $rexterProfile = '#'. $this->profile_id.':4';

        $binding->command('UPDATE Profile REMOVE followers');

        $query->put(array('followers' => array('David' => $davidProfile)), 'Profile')
              ->where('@rid = ?', $rexterProfile);

        $this->assertHttpStatus(200, $this->doQuery($query, $binding));

        $records = $binding->command('SELECT FROM ' . $rexterProfile)->getResult();
        $this->assertInstanceOf('\stdClass', $records[0]->followers);
        $this->assertSame($davidProfile, $records[0]->followers->David);
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
