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
      $this->orient = new Binding($this->driver, '127.0.0.1', '2480', 'admin', 'admin',$dbName);
      
      $this->query = new Query();
  }
  
  public function testASimpleSelect()
  {
      $this->query->from(array('address'));
      
      $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw())->getStatusCode());
      
      $this->query->select(array('@version','street'));
      
      $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw())->getStatusCode());
  }
  
  public function testAComplexSelect()
  {
    $query = new Query();
    $query->index('index_name','unique');
    
    $this->assertStatusCode(self::_204, $this->orient->command($query->getRaw())->getStatusCode());
    
    $this->query->from(array('index:index_name'))->between('key','10.0','10.1');
    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw())->getStatusCode());
    
    $query = new Query();
    $query->unindex('index_name');
    
    $this->assertStatusCode(self::_204, $this->orient->command($query->getRaw())->getStatusCode());    
    
  }
}