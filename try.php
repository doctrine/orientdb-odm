<?php

spl_autoload_register(function ($className){
    $path = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';

    if (file_exists($path))
    {
      include($path);
    }
  }
);

$driver = new Orient\Http\Curl();
$orient = new Orient\Foundation\Binding($driver, '127.0.0.1', '2480', 'admin', 'admin', 'demo');

$response = $orient->query("select from Person where any() traverse(0,3) ( @rid = 28:0 ) and @rid <> 28:0");

$output = json_decode($response->getBody());

foreach ($output->result as $friend)
{
  var_dump($friend->name);
}

