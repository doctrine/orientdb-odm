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
 * @author     Alessnadro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */
class BindingTest extends PHPUnit_Framework_TestCase
{
  const _401 = 'HTTP/1.1 401 Unauthorized';
  const _200 = 'HTTP/1.1 200 OK';
  const _204 = 'HTTP/1.1 204 OK';
  const _500 = 'HTTP/1.1 500 Internal Server Error';

  public function testConnect()
  {
    $driver = new Orient\Http\Curl();
    $orient = new Orient\Foundation\Binding($driver, '127.0.0.1', '2480', 'admin', 'admin');

    $orient->setAuthentication('admin', 'admin');
    $orient->setDatabase('demo');
    $this->assertEquals(self::_200, $orient->connect('demo')->getStatusCode());
    $this->assertEquals(self::_200, $orient->cluster('Address')->getStatusCode());
    $this->assertEquals(self::_200, $orient->cluster('Address',false,1)->getStatusCode());


    // ===========
    // = Cluster =
    // ===========
    $result  = json_decode($orient->cluster('Address',false,1)->getBody(),true);
    $this->assertEquals('Address', $result['schema']['name'], 'The cluster is wrong');
    $this->assertEquals(1, count($result['result']), 'The limi is wrong');
    
    $result  = json_decode($orient->cluster('City',false,10)->getBody(),true);
    $this->assertEquals('City', $result['schema']['name'], 'The cluster is wrong');
    $this->assertEquals(10, count($result['result']),  'The limit is wrong' );

    // ===========
    // = Command =
    // ===========
    $this->assertEquals(self::_200, $orient->command('select from Address')->getStatusCode(), 'execute a simple select');
    $this->assertEquals(self::_200, $orient->command("select from City where name = 'Rome'")->getStatusCode(), 'execute a select with WHERE condition');
    $this->assertEquals(self::_200, $orient->command('select from City where name = "Rome"')->getStatusCode(), 'execute another select with WHERE condition');
    $this->assertEquals(self::_500, $orient->command("OMG OMG OMG")->getStatusCode(), 'execute a wrong SQL command');
    # HTTPTODO: status code should be 400 or 404

    // ===========
    // = Database =
    // ===========
    $this->assertEquals(self::_200, $orient->getDatabase('demo')->getStatusCode(), 'get informations about an existing database');
    $this->assertEquals(self::_401, $orient->getDatabase("OMGOMGOMG")->getStatusCode(), 'get informations about a non-existing database');
    # HTTPTODO: status code should be  404
    //$orient->setAuthentication('root', 'EAD5A71FAD21DB3216567E4BACD711C3E39AD0C953CEAEC4EC1464A5C645A6FC');
    // ohhhh problems, can't delete DB
    //$this->assertEquals(self::_204, $orient->postDatabase('db.' . rand(0, 999))->getStatusCode(), 'ry to create a database that exists');

    

    //var_dump($orient->connect('demo')->getStatusCode());
    //$orient->setAuthentication('server', 'server');
    //var_dump($orient->getServer('demo')->getStatusCode());
    //$orient->setAuthentication('admin', 'admin');
    //var_dump($orient->deleteClass('Test')->getStatusCode());
    //var_dump($orient->getClass('Test')->getStatusCode());
    //var_dump($orient->postClass('Test')->getStatusCode());
    //var_dump($orient->getClass('Test')->getStatusCode());
    //var_dump($orient->disconnect()->getBody());
  }
}

