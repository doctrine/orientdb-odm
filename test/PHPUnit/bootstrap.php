<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once(__DIR__.'/../../autoload.php');

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'Congow\Orient\Proxy' => __DIR__.'/../../proxies/',
    'test' => __DIR__.'/../../',
));

$loader->register();
