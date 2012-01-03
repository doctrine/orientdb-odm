<?php

/**
 * ManagerTest class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Integration;

use test\PHPUnit\TestCase;
use Congow\Orient\Query;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;

class ManagerTest extends TestCase
{
    public function setup()
    {
        $mapper          = new Mapper(__DIR__ . "/../../proxies");
        $mapper->setDocumentDirectories(array('./test/Integration/Document' => 'test'));
        $client          = new \Congow\Orient\Http\Client\Curl(false, 10);
        $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
        $this->manager   = new Manager($mapper, $protocolAdapter);
    }
    
    public function testExecutionOfASelect()
    {
        $query      = new Query(array('Address'));
        $addresses  = $this->manager->execute($query);
        
        $this->assertTrue(count($addresses) > 0);
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }
    
    public function testFindingARecordWithAnExecuteReturnsAnArrayHowever()
    {
        $query      = new Query(array('13:0'));
        $addresses  = $this->manager->execute($query);
        
        $this->assertEquals(1, count($addresses));
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }
    
    public function testExecutionOfAnUpdate()
    {
        $query      = new Query(array('Address'));
        $query->update('Address')->set(array('my' => 'yours'))->where('@rid = ?', '1:10000');
        $result  = $this->manager->execute($query);
        
        $this->assertTrue($result);
        $this->assertInternalType('boolean', $result);
    }
    
    /**
     * @expectedException \Congow\Orient\Exception\Query\SQL\Invalid
     */
    public function testAnExceptionGetsRaisedWhenExecutingAWrongQuery()
    {
        $query      = new Query(array('Address'));
        $query->update('Address')->set(array())->where('@rid = ?', '1:10000');
        $result  = $this->manager->execute($query);
    }
    
    public function testFindingARecord()
    {
        $address    = $this->manager->find('13:0');
        
        $this->assertInstanceOf("test\Integration\Document\Address", $address);
    }
    
    public function testFindingARecordWithAFetchPlan()
    {
        $post       = $this->manager->find('30:0', '*:-1');
        $this->assertInternalType('array', $post->comments);
        $this->assertFalse($post->comments instanceOf \Congow\Orient\ODM\Proxy\Collection);
    }
    
        
    public function testGettingARelatedRecord()
    {
        $address    = $this->manager->find('13:0');
        $this->assertInstanceOf("test\Integration\Document\Country", $address->getCity());
    }
        
    public function testGettingARelatedCollection()
    {
        $post       = $this->manager->find('30:0');
        $comments   = $post->getComments();
        
        $this->assertInstanceOf("test\Integration\Document\Comment", $comments[0]);
    }
    
    /**
     * @expectedException \Congow\Orient\Exception\ODM\OClass\NotFound
     */
    public function testLookingForANonMappedTypeRaisesAnException()
    {
        $mapper          = new Mapper(__DIR__ . "/../../proxies");
        $mapper->setDocumentDirectories(array('./docs' => '\\'));
        $client          = new \Congow\Orient\Http\Client\Curl(false, 10);
        $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
        $this->manager   = new Manager($mapper, $protocolAdapter);
        $address = $this->manager->find('13:0');
    }
    
    public function testFindingANonExistingRecord()
    {
        $address    = $this->manager->find('13:2000');
        
        $this->assertInternalType("null", $address);
    }
    
    public function testFindingSomeRecords()
    {
        $addresses    = $this->manager->findRecords(array('13:0', '13:1'));
        
        $this->assertEquals(2, count($addresses));
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }
    
    /**
     * @expectedException \Congow\Orient\Exception\Query\SQL\Invalid
     */
    public function testFindingSomeGoodAndSomeWrongRecords()
    {
        $this->manager->findRecords(array('13:0', '13:700000'));
    }
    
    /**
     * @expectedException \Congow\Orient\Exception\Query\SQL\Invalid
     */
    public function testFindingSomeRecordsAndSomeAreWrongThrowsAnException()
    {
        $this->manager->findRecords(array('13:0', '13:1000'));
    }   
    
    public function testExecutingASelectOfASingleRecordReturnsAnArrayWithOneRecord()
    {
        $query = new Query(array('Address'));
        $query->where('@rid = ?', '13:0');
        
        $this->assertInternalType('array', $this->manager->execute($query));
        $this->assertEquals(1, count($this->manager->execute($query)));
    }
    
    public function testExecutionWithNoOutput()
    {
        $query = new Query();
        $query->update('Address')->set(array('type' => 'Residence'));
        
        $this->assertInternalType('bool', $this->manager->execute($query));
        $this->assertEquals(true, $this->manager->execute($query));
    }
    
    public function testFlushingAnObject()
    {
        $repo = $this->manager->getRepository('test\Integration\Document\Address');
        $collection = $repo->findAll();
        
        $startCount = count($collection);
        
        $address = new \test\Integration\Document\Address();
        //$address->setCity('Rome');
        
        $this->manager->persist($address);
        $this->manager->flush();
        
        $this->assertEquals($startCount + 1 , count($repo->findAll()));
    }
    
    public function testAnObjectPersisted2TimesGetsSavedWithTheLastValuesWhenFlushing()
    {
        throw new \Exception();
    }
    
    public function testPersistingAnUpdate() 
    {
        throw new \Exception();
    }
    
    public function testPersistingADeletion() 
    {
        throw new \Exception();
    }
    
    public function testPersistingADeletionOnAPersistedObject() 
    {
        throw new \Exception();
    }
    
    public function testPersistingADeletionWhichInvolvesCascadingDeletions() 
    {
        throw new \Exception();
    }
}