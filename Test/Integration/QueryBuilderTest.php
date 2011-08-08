<?php

/**
 * QueryBuilderTest class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Test\Integration;

use Orient\Test\PHPUnit\TestCase;
use Orient\Query;
use Orient\Http\Client\Curl;
use Orient\Foundation\Binding;

class QueryBuilderTest extends TestCase
{
    const _200 = 'HTTP/1.1 200 OK';
    const _201 = 'HTTP/1.1 201 Created';
    const _204 = 'HTTP/1.1 204 OK';
    const _401 = 'HTTP/1.1 401 Unauthorized';
    const _404 = 'HTTP/1.1 404 Not Found';
    const _500 = 'HTTP/1.1 500 Internal Server Error';

    public function setup()
    {
        $this->driver = new Curl();
        $dbName = 'demo';
        $this->orient = new Binding($this->driver, '127.0.0.1', '2480', 'admin', 'admin', $dbName);
        $this->query = new Query();
    }

    public function assertFirstRid($rid, $response)
    {
        $res = json_decode($response->getBody());
        $message = 'The first RID of the results is ' . $rid;
        $property = '@rid';

        $this->assertEquals('#' . $rid, $res->result[0]->$property, $message);
    }

    public function testASimpleSelect()
    {
        $this->query->from(array('address'));

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->select(array('@version', 'street'));

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testTheRangeOfASelect()
    {
        $this->query->from(array('Address'))->range('13:1');

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->range(null, '12');

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->range('10.0');

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->range('10.1', false);

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->range('13:100', '13:109');

        $this->assertStatusCode(self::_200, $this->query());

        $this->assertEquals(10, $this->countResults($this->query()));

        $this->query->range('13:100', '13:101');

        $this->assertEquals(2, $this->countResults($this->query()));
    }

    public function testLimitingASelect()
    {
        $this->query->from(array('Address'))->limit(20);

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertEquals(20, $this->countResults($this->query()));

        $this->query->from(array('Address'))->limit(30);
        $this->query->from(array('Address'))->limit(20);

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertEquals(20, $this->countResults($this->query()));

        $this->query->from(array('Address'))->limit('a');

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertTrue((bool) ($this->countResults($this->query()) > 21));
    }

    public function testSelectingByRIDs()
    {
        $this->query->from(array('13:100'));

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertFirstRid('13:100', $this->query());
    }

    public function testOrderingTheQuery()
    {
        $this->query->from(array('13:100', '13:101'))->orderBy('rid ASC')->orderBy('street DESC');

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertFirstRid('13:100', $this->query());

        $this->query->orderBy('rid DESC', false);

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertFirstRid('13:101', $this->query());
    }

    public function testDoingAComplexSelect()
    {
        $this->query->limit(10);
        $this->query->limit(20);
        $this->query->from(array('13:2', '13:4'), false);
        $this->query->select(array('rid', 'street'));
        $this->query->select(array('type'));
        $this->query->range('13:2');
        $this->query->range(null, '13:4');
        $this->query->orderBy('street ASC');

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testInsertARecord()
    {
        $countQuery = $this->orient->command('SELECT FROM Address');
        $count = $this->countResults($countQuery);

        $this->query->insert()
                ->fields(array('street', 'type', 'city'))
                ->values(array('5th avenue', 'villetta', '#13:0'))
                ->into('Address');

        $this->assertStatusCode(self::_200, $this->query());
        $recount = $this->countResults($this->orient->command('SELECT FROM Address'));
        $this->assertEquals($count + 1, $recount);
    }

    /**
     * @depends testInsertARecord
     */
    public function testADelete()
    {
        $countQuery = $this->orient->command('SELECT FROM Address');
        $count = $this->countResults($countQuery);

        $this->query->delete('Address')
                ->where('street = ?', '5th avenue')
                ->orWhere('type = "villetta"');

        $this->assertStatusCode(self::_200, $this->query());
        $recount = $this->countResults($this->orient->command('SELECT FROM Address'));
        $this->assertEquals($count - 1, $recount);
    }

    public function testInsertAnotherRecord()
    {
        $countQuery = $this->orient->command('SELECT FROM Address');
        $count = $this->countResults($countQuery);

        $this->query->insert()
                ->fields(array('street', 'type', 'city'))
                ->values(array('5th avenue', 'villetta', '#13:0'))
                ->into('Address');

        $this->assertStatusCode(self::_200, $this->query());
        $recount = $this->countResults($this->orient->command('SELECT FROM Address'));
        $this->assertEquals($count + 1, $recount);
    }

    /**
     * @depends testInsertAnotherRecord
     */
    public function testTruncatingARecord()
    {
        $countQuery = $this->orient->command('SELECT FROM Address');
        $count = $this->countResults($countQuery);

        $this->query
                ->from(array('Address'))
                ->where('street = ?', '5th avenue')
                ->orWhere('type = "villetta"');
        
        $res = json_decode($this->query()->getBody());
        
        $this->query->truncate(substr($res->result[0]->{"@rid"}, 1));
        $this->assertStatusCode(self::_200, $this->query());
        $recount = $this->countResults($this->orient->command('SELECT FROM Address'));
        $this->assertEquals($count - 1, $recount);
    }

    public function testGrantingACredential()
    {
        $this->query->grant('READ')
                ->to('reader')
                ->on('Address');

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testRevokingACredential()
    {
        $this->query->revoke('READ')
                ->to('reader')
                ->on('Address');

        $this->assertStatusCode(self::_200, $this->query());
    }

    /**
     * @todo open a bug in the traccke of OrientDB
     * count(*)  returns only 20 results.
     */
    public function testCreateAnIndex()
    {
        $this->query->index('index_name_2', 'unique');

        $this->assertStatusCode(self::_200, $this->query());
        $this->assertEquals(0, $this->query()->getBody());

        $this->query = new Query();
        $this->query->index('in', 'unique', 'OGraphEdge');

        $this->assertStatusCode(self::_200, $this->query());
        $countQuery = $this->orient->command('SELECT FROM OGraphEdge');
        $count = $this->countResults($countQuery);
        $this->assertEquals($count, $this->query()->getBody());
    }

    public function testCountingAnIndexSize()
    {
        $this->query->indexCount('index_name_2');

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testExecutingAIndexLookup()
    {
        $this->query->lookup('index_name_2');

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->where('key = ?', 2);

        $this->assertStatusCode(self::_200, $this->query());

        $this->query->where('fakekey = ?', 2);

        $this->assertStatusCode(self::_500, $this->query());

        $this->query = new Query();
        $this->query->from(array('index:index_name_2'))->between('key', '10.0', '10.1');
        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testAddingAnEntryToAnIndex()
    {
        $this->query->indexCount('index_name_2');
        $count = $this->countResults($this->query());
        $this->query->indexPut('index_name_2', 'k', '13:100');

        $this->assertStatusCode(self::_204, $this->query());
        
        $this->query->indexCount('index_name_2');
        $recount = $this->countResults($this->query());
        $this->assertEquals($count + 1, $recount);
    }

    public function testRemovingAnEntryToAnIndex()
    {
        $this->query->indexCount('index_name_2');
        $count = $this->countResults($this->query());
        $this->query->indexRemove('index_name_2', 'k');

        $this->assertStatusCode(self::_200, $this->query());
        $this->query->indexCount('index_name_2');
        $recount = $this->countResults($this->query());
        $this->assertEquals($count - 1, $recount);
    }

    public function testDroppingAnIndex()
    {
        $this->query->indexCount('index_name_2');
        $this->assertStatusCode(self::_200, $this->query());
        $this->query->unindex('index_name_2');

        $this->assertStatusCode(self::_204, $this->query());
        $this->query->indexCount('index_name_2');
        $this->assertStatusCode(self::_500, $this->query());

        $this->query->unindex('in', 'OGraphEdge');

        $this->assertStatusCode(self::_204, $this->query());
    }

    public function testFindingAReference()
    {
        $this->query->findReferences('13:0');

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testCreatingAClass()
    {
        $this->time = microtime();
        $class = 'MyCustomTestClass' . $this->time;
        $this->query->create($class);

        $this->assertStatusCode(self::_200, $this->query());

        return $class;
    }
    
    /**
     * @depends testCreatingAClass
     */
    public function testTruncatingAClass($class)
    {
        $this->query->truncate($class);

        $this->assertStatusCode(self::_200, $this->query());
    }
    
    /**
     * @depends testCreatingAClass
     */
    public function testTruncatingACluster($class)
    {
        $this->query->truncate($class, true);

        $this->assertStatusCode(self::_200, $this->query());
    }

    /**
     * @depends testCreatingAClass
     */
    public function testAlteringAClass($class)
    {
        $this->query->alter($class, 'SUPERCLASS', 'OUser');

        $this->assertStatusCode(self::_204, $this->query());

        return $class;
    }

    /**
     * @depends testAlteringAClass
     */
    public function testCreatingAProperty($class)
    {
        $this->query->create($class, 'customTestProperty', 'string');

        $this->assertStatusCode(self::_200, $this->query());

        return $class;
    }

    /**
     * @depends testCreatingAProperty
     */
    public function testAlteringAProperty($class)
    {
        $this->query->alterProperty($class, 'customTestProperty', 'notnull', 'false');

        $this->assertStatusCode(self::_204, $this->query());

        $this->query->alterProperty($class, 'customTestProperty', 'notnull', 'true');

        $this->assertStatusCode(self::_204, $this->query());

        return $class;
    }

    /**
     * @depends testAlteringAProperty
     */
    public function testDroppingAProperty($class)
    {
        $this->query->drop($class, 'customTestProperty');

        $this->assertStatusCode(self::_204, $this->query());

        return $class;
    }

    /**
     * @depends testAlteringAClass
     */
    public function testDroppingClass($class)
    {
        $this->query->drop($class);

        $this->assertStatusCode(self::_204, $this->query());

        return $class;
    }

    public function testLinkingTwoObjects()
    {
        $this->query->link('Company', 'id', 'in', true)->with('ORole', 'id');

        $this->assertStatusCode(self::_200, $this->query());
    }

    public function testUpdating()
    {        
        $this->query->update('Address')->set(array('nick' => 'Luca'));
        $this->query->orWhere('@rid = ?', '13:101');
        
        $this->assertStatusCode(self::_200, $this->query());
        
        $res = json_decode($this->orient->command('SELECT FROM Address WHERE @rid = #13:101')->getBody());
        $document = $res->result[0];
        
        $this->assertEquals('Luca', $document->nick);
        
        $this->query->update('Address')->set(array('nick' => 'Luca2'));
        $this->query->orWhere('@rid = ?', '13:101');
        
        $this->assertStatusCode(self::_200, $this->query());
        
        $res = json_decode($this->orient->command('SELECT FROM Address WHERE @rid = #13:101')->getBody());
        $document = $res->result[0];
        
        $this->assertEquals('Luca2', $document->nick);
    }

    public function testAddingALink()
    {
        $res = json_decode($this->orient->command('SELECT FROM 26:1')->getBody());
        $document = $res->result[0];
        $count = count($document->comments);
        
        $this->query->add(array('comments' => '26:0'), 'post');
        $this->query->where('@rid = ?', '26:1');

        $this->assertStatusCode(self::_200, $this->query());
        
        $res = json_decode($this->orient->command('SELECT FROM 26:1')->getBody());
        $document = $res->result[0];

        $res = json_decode($this->orient->command('SELECT FROM 26:1')->getBody());
        $document = $res->result[0];
        $recount = count($document->comments);
        
        $this->assertEquals($count + 1, $recount);
    }

    /**
     * @depends testAddingALink
     */
    public function testRemovingALink()
    {
        $res = json_decode($this->orient->command('SELECT FROM 26:1')->getBody());
        $document = $res->result[0];
        $count = count($document->comments);
        
        $this->query->remove(array('comments' => '26:0'), 'post');
        $this->query->where('@rid = ?', '26:1');

        $this->assertStatusCode(self::_200, $this->query());
        
        $res = json_decode($this->orient->command('SELECT FROM 26:1')->getBody());
        $document = $res->result[0];
        $recount = count($document->comments);
        
        $this->assertEquals($count - 1, $recount);
    }

    public function testPuttingALink()
    {
        $this->query->put(array('knows' => array('Johnny' => '9:2')), 'account');
        $this->query->where('@rid = ?', '9:1');

        $this->assertStatusCode(self::_200, $this->query());

        $res = json_decode($this->orient->command('SELECT FROM 9:1')->getBody());

        $document = $res->result[0];
        $this->assertInstanceOf('stdClass', $document->knows);
        $this->assertEquals('#9:2', $document->knows->Johnny);
    }
    
    public function testTruncatingNonExistingClass()
    {
        $this->query->truncate('OMNOMNOMOMNOMNOMNO');

        $this->assertStatusCode(self::_500, $this->query());
    }
    
    public function testTruncatingNonExistingCluster()
    {
        $this->query->truncate('OMNOMOMNOMNOMNOM', true);

        $this->assertStatusCode(self::_500, $this->query());
    }

    protected function countResults(\Orient\Http\Response $response)
    {
        $response = json_decode($response->getBody());
        $property = 'count(*)';

        if (array_key_exists(0, $response->result) && property_exists($response->result[0], $property))
        {
            return $response->result[0]->$property;
        }
        elseif (array_key_exists(0, $response->result) && property_exists($response->result[0], 'size'))
        {
            return $response->result[0]->size;
        }
        else
        {
            if (property_exists($response, 'result'))
            {
                return count($response->result);
            }
        }

        throw new \Exception('Unable to retrieve a count from the given response.');
    }

    protected function query()
    {
        return $this->orient->command($this->query->getRaw());
    }

}