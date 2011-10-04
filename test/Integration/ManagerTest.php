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
        $mapper->setDocumentDirectories(array('./test/Integration/Document' => '\\'));
        $client          = new \Congow\Orient\Http\Client\Curl(false, 10);
        $binding         = new \Congow\Orient\Foundation\Binding($client, '127.0.0.1', '2480', 'admin', 'admin', 'demo');
        $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
        $this->manager   = new Manager($mapper, $protocolAdapter);
    }
    
    public function testExecutionOfASelect()
    {
        $query      = new Query(array('Address'));
        $addresses  = $this->manager->execute($query);
        
        $this->assertEquals(20, count($addresses));
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
    
        
    public function testGettingARelatedRecord()
    {
        $address    = $this->manager->find('13:0');
        
        $this->assertInstanceOf("test\Integration\Document\Country", $address->getCity());
    }
    
    /**
     * @expectedException \Congow\Orient\Exception\ODM\OClass\NotFound
     */
    public function testLookingForANonMappedTypeRaisesAnException()
    {
        $mapper          = new Mapper(__DIR__ . "/../../proxies");
        $mapper->setDocumentDirectories(array('./docs' => '\\'));
        $client          = new \Congow\Orient\Http\Client\Curl(false, 10);
        $binding         = new \Congow\Orient\Foundation\Binding($client, '127.0.0.1', '2480', 'admin', 'admin', 'demo');
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
    public function testFindingSomeRecordsAndSomeAreWrongThrowsAnException()
    {
        $this->manager->findRecords(array('13:0', '13:1000'));
    }
}