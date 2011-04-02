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
  public function testConnect()
  {
    $driver = new Orient\Http\Curl();
    $orient = new Orient\Foundation\Binding($driver, '127.0.0.1', '2480', 'admin', 'admin');

    $orient->setAuthentication('admin', 'admin');
    $orient->setDatabase('demo');
    $this->assertEquals('HTTP/1.1 200 OK', $orient->connect('demo')->getStatusCode());
    $this->assertEquals('HTTP/1.1 200 OK',$orient->cluster('Address')->getStatusCode());
    $this->assertEquals('HTTP/1.1 200 OK',$orient->cluster('Address',false,1)->getStatusCode());


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
    $this->assertEquals('HTTP/1.1 200 OK', $orient->command('select from Address')->getStatusCode(), 'execute a simple select');
    $this->assertEquals('HTTP/1.1 200 OK', $orient->command("select from City where name = 'Rome'")->getStatusCode(), 'execute a select with WHERE condition');
    $this->assertEquals('HTTP/1.1 200 OK', $orient->command('select from City where name = "Rome"')->getStatusCode(), 'execute another select with WHERE condition');
    $this->assertEquals('HTTP/1.1 500 Internal Server Error', $orient->command("OMG OMG OMG")->getStatusCode(), 'execute a wrong SQL command');

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

