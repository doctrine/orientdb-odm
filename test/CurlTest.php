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
class CurlTest extends PHPUnit_Framework_TestCase
{
  public function testGet()
  {
    $client = new Orient\Http\Curl();
    $response = $client->get('127.0.0.1');
    
    $this->assertInstanceOf('Orient\Http\Response', $response);
    $this->assertEquals('HTTP/1.1 200 OK', $response->getStatusCode());
  }

  public function testPost()
  {
    $client       = new Orient\Http\Curl();

    $postData     = '1=postdata&2=poststuff';
    $postPrinter  = '127.0.0.1/orient/test/postprinter.php';
    $response     = $client->post($postPrinter, $postData);

    $this->assertEquals('postdata,poststuff', $response->getBody());
    $this->assertEquals('HTTP/1.1 200 OK', $response->getStatusCode());
  }

  public function testDelete()
  {
    $client       = new Orient\Http\Curl();
    $response     = $client->delete('127.0.0.1');

    $this->assertEquals('HTTP/1.1 405 Method Not Allowed', $response->getStatusCode());
  }

  public function testPut()
  {
    $client       = new Orient\Http\Curl();
    $putPrinter   = '127.0.0.1:8080/orient/test/putprinter.php';
    $putData     = '1=postdata&2=poststuff';
    $response     = $client->put($putPrinter, $putData);

    $this->assertEquals('HTTP/1.1 200 OK', $response->getStatusCode());
    $this->assertEquals('postdata,poststuff', $response->getBody());
  }
}

