<?php

/**
 * HttpAdapterTest class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace test\Integration\Protocol;

use test\PHPUnit\TestCase;
use Congow\Orient\Foundation\Protocol\Adapter\Http as HttpAdapter;
use Congow\Orient\Http\Client\Curl;

class HttpAdapterTest extends TestCase
{
    public function setup()
    {
        $driver = new Curl(false, TEST_ODB_TIMEOUT);
        $binding = new \Congow\Orient\Foundation\Binding($driver, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $this->adapter = new HttpAdapter($binding);
    }
    
    public function testFindingARecord()
    {
        $query = 'SELECT FROM post WHERE @rid = 26:0';
        
        $this->assertTrue($this->adapter->execute($query, true));
        $this->assertInternalType('array', $this->adapter->getResult());
    }
    
    public function testFindingANonExistingRecord()
    {
        $query = 'SELECT FROM post WHERE @rid = 26:45646156';
        
        $this->assertTrue($this->adapter->execute($query, true));
        $this->assertInternalType('array', $this->adapter->getResult());
    }
    
    public function testExecutingAQuery()
    {
        $query = 'SELECT FROM post WHERE @rid = 26:0';
        
        $this->assertTrue($this->adapter->execute($query));
        $this->assertInternalType('null', $this->adapter->getResult());
    }
    
    /**
     * @expectedException Congow\Orient\Exception\Query\SQL\Invalid
     */
    public function testExecutingAWrongQuery()
    {
        $query = 'OMNMOMNOMNOMOMN';
        
        $this->adapter->execute($query);
    }
    
    // public function testFindingARecord()
    //     {   
    //         $this->assertInstanceOf('stdClass', $this->adapter->find('26:0'));
    //     }
}