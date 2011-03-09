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

