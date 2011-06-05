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
      
      $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
      
      $this->query->select(array('@version','street'));
      
      $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }
  
  public function testASelectThoughAnIndex()
  {
    $query = new Query();
    $query->index('index_name','unique');
    
    $this->assertStatusCode(self::_204, $this->orient->command($query->getRaw()));
    
    $this->query->from(array('index:index_name'))->between('key','10.0','10.1');
    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
    
    $query = new Query();
    $query->unindex('index_name');
    
    $this->assertStatusCode(self::_204, $this->orient->command($query->getRaw()));    
  }

  public function testTheRangeOfASelect()
  {
    $this->query->from(array('Address'))->range('12:1');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->range(null, '12');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->range('10.0');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->range('10.1', false);

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->range('10.1', '10.2');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }

  public function testLimitingASelect()
  {
    $this->query->from(array('Address'))->limit(20);

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->from(array('Address'))->limit('20');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));

    $this->query->from(array('Address'))->limit('a');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }

  public function testSelectingByRIDs()
  {
    $this->query->from(array('12:0', '12:1'));

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }

  public function testOrderingTheQuery()
  {
    $this->query->from(array('12:0', '12:1'))->orderBy('rid ASC')->orderBy('street DESC');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }

  public function testDoingAComplexSelect()
  {
    $this->query->limit(10);
    $this->query->limit(20);
    $this->query->from(array('12:2', '12:4'), false);
    $this->query->select(array('rid', 'street'));
    $this->query->select(array('type'));
    $this->query->range('12:2');
    $this->query->range(null, '12:4');
    $this->query->orderBy('street ASC');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }

  public function testInsertARecord()
  {
    $this->query->insert()
                ->fields(array('street', 'type', 'city'))
                ->values(array('5th avenue', 'villa', '#13:0'))
                ->into('Address');

    $this->assertStatusCode(self::_200, $this->orient->command($this->query->getRaw()));
  }
}