<?php

require_once __DIR__.'/../../src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
 
use Symfony\Component\ClassLoader\UniversalClassLoader;
 
$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'test'                      => __DIR__ . "/../..",
    'Congow\Orient'             => __DIR__ . "/../../src",
    'Congow\Orient\Proxy'       => __DIR__ . "/../../proxies",
    'Doctrine\Common'           => 'src/Doctrine/lib/',
    'Symfony'                   => 'src',
));
$loader->register();