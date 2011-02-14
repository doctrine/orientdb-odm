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
$orient = new Orient\Foundation\Binding($driver, '127.0.0.1', '2480', 'admin', 'admin');

$orient->setAuthentication('admin', 'admin');
$orient->setDatabase('demo');
var_dump($orient->class_('MyOdino', false, 'post'));