<?php

spl_autoload_register(function ($className){
    $path = __DIR__ . '/../' . str_replace('\\', '/', $className) . '.php';

    if (file_exists($path))
    {
      include($path);
    }
  }
);

/**
 * BindingTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
class BindingTest extends PHPUnit_Framework_TestCase
{
  const _200 = 'HTTP/1.1 200 OK';
  const _201 = 'HTTP/1.1 201 Created';
  const _204 = 'HTTP/1.1 204 OK';
  const _401 = 'HTTP/1.1 401 Unauthorized';
  const _404 = 'HTTP/1.1 404 Not Found';
  const _500 = 'HTTP/1.1 500 Internal Server Error';

  public function initialize()
  {
    $this->driver = new Orient\Http\Client\Curl();
    $this->orient = new Orient\Foundation\Binding($this->driver, '127.0.0.1', '2480', 'admin', 'admin');
  }

  public function testConnect()
  {
    $this->initialize();
    $this->orient->setAuthentication('', '');
    $this->orient->setDatabase('demo');
    
    $this->assertEquals(self::_401, $this->orient->connect('ZOMG')->getStatusCode());
    $this->orient->setAuthentication('admin', 'admin');
    $this->assertEquals(self::_200, $this->orient->connect('demo')->getStatusCode());
  }

  public function testClass()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');

    $this->assertEquals(self::_500, $this->orient->getClass('OMG')->getStatusCode(), 'get a non existing class');
    $this->assertEquals(self::_201, $this->orient->postClass('OMG')->getStatusCode(), 'create a class');
    $this->assertEquals(self::_204, $this->orient->deleteClass('OMG')->getStatusCode(), 'delete a class');
  }

  public function testCluster()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');
    
    $this->assertEquals(self::_200, $this->orient->cluster('Address')->getStatusCode());
    $this->assertEquals(self::_200, $this->orient->cluster('Address',false,1)->getStatusCode());
    $result  = json_decode($this->orient->cluster('Address',false,1)->getBody(),true);
    $this->assertEquals('Address', $result['schema']['name'], 'The cluster is wrong');
    $this->assertEquals(1, count($result['result']), 'The limi is wrong');

    $result  = json_decode($this->orient->cluster('City',false,10)->getBody(),true);
    $this->assertEquals('City', $result['schema']['name'], 'The cluster is wrong');
    $this->assertEquals(10, count($result['result']),  'The limit is wrong' );
  }

  public function testCommand()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');

    $this->assertEquals(self::_200, $this->orient->command('select from Address')->getStatusCode(), 'execute a simple select');
    $this->assertEquals(self::_200, $this->orient->command("select from City where name = 'Rome'")->getStatusCode(), 'execute a select with WHERE condition');
    $this->assertEquals(self::_200, $this->orient->command('select from City where name = "Rome"')->getStatusCode(), 'execute another select with WHERE condition');
    $this->assertEquals(self::_500, $this->orient->command("OMG OMG OMG")->getStatusCode(), 'execute a wrong SQL command');
    # HTTPTODO: status code should be 400 or 404
  }

  public function testDatabase()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');

    $this->assertEquals(self::_200, $this->orient->getDatabase('demo')->getStatusCode(), 'get informations about an existing database');
    $this->assertEquals(self::_401, $this->orient->getDatabase("OMGOMGOMG")->getStatusCode(), 'get informations about a non-existing database');
    # HTTPTODO: status code should be  404
    //$this->orient->setAuthentication('root', 'EAD5A71FAD21DB3216567E4BACD711C3E39AD0C953CEAEC4EC1464A5C645A6FC');
    // ohhhh problems, can't delete DB
    //$this->assertEquals(self::_204, $this->orient->postDatabase('db.' . rand(0, 999))->getStatusCode(), 'ry to create a database that exists');
  }

  public function testQuery()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');

    $this->orient->setDatabase('demo');
    $this->assertEquals(self::_200, $this->orient->query('select from Address')->getStatusCode(), 'executes a SELECT');
    $this->assertEquals(self::_500, $this->orient->query("update Profile set online = false")->getStatusCode(), 'tries to xecute an UPDATE with the quesry command');
  }

  public function testDocument()
  {
    $this->initialize();
    $this->orient->setDatabase('demo');
    $this->orient->setAuthentication('admin', 'admin');

    $this->assertEquals(self::_500, $this->orient->getDocument('991')->getStatusCode(), 'retrieves a document with an invalid RID');
    $this->assertEquals(self::_404, $this->orient->getDocument('9:0')->getStatusCode(), 'retrieves a non existing document');
    $this->assertEquals(self::_500, $this->orient->getDocument('999:0')->getStatusCode(), 'retrieves a document from a non existing cluster');
    $this->assertEquals(self::_200, $this->orient->getDocument('1:0')->getStatusCode(), 'retrieves a valid document');

    $document = json_encode(array('_class' => 'Address', 'name' => 'Test'));

    $createDocument = $this->orient->postDocument($document);
    $rid            = $createDocument->getBody();
    $this->assertEquals(self::_201, $createDocument->getStatusCode(), 'creates a valid document');
    $document = json_encode(array('@rid' => $rid,'_class' => 'Address', 'name' => 'Test'));
    $this->assertEquals(self::_200, $this->orient->putDocument($rid, $document)->getStatusCode(), 'updates a valid document');
    $this->assertEquals(self::_500, $this->orient->putDocument('9991', $document)->getStatusCode(), 'updates a non valid document');
    $this->assertEquals(self::_204, $this->orient->deleteDocument($createDocument->getBody())->getStatusCode(), 'deletes a valid document');
    $this->assertEquals(self::_500, $this->orient->deleteDocument('999:1')->getStatusCode(), 'deletes a non existing document');
    $this->assertEquals(self::_500, $this->orient->deleteDocument('9991')->getStatusCode(), 'deletes a non valid document');
  }

  /**
   * @expectedException Orient\Exception\Http\Response\Void
   */
  public function testClient()
  {
    $this->initialize();
    $this->driver->get('1.1.1.1');
  }
}

