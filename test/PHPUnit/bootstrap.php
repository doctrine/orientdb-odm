<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once(__DIR__.'/../../autoload.php');

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'Doctrine\OrientDB\Proxy' => __DIR__.'/../../test/proxies/',
    'test' => __DIR__.'/../../',
));

$loader->register();

