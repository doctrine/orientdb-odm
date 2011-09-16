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
        $this->adapter = new HttpAdapter(new Curl, '127.0.0.1', '2480','admin','admin','demo');
    }
    
    public function testFindingARecordWithExecute()
    {
        $query = 'SELECT FROM 26:0';
        
        $this->assertInternalType('array', $this->adapter->execute($query));
    }
    
    // public function testFindingARecord()
    //     {   
    //         $this->assertInstanceOf('stdClass', $this->adapter->find('26:0'));
    //     }
}