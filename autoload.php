<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;

$composer = __DIR__.'/vendor/.composer/autoload.php';

if (file_exists($composer)) {
    require_once($composer);
}
else {
    require_once __DIR__.'/vendor/symfony/class-loader/Symfony/Component/ClassLoader/UniversalClassLoader.php';

    $loader = new UniversalClassLoader();

    $loader->registerNamespaces(array(
        'Congow\Orient'                 => __DIR__.'/src',
        'Doctrine\Common'               => __DIR__.'/vendor/doctrine/common/lib/',
        'Symfony\Component\ClassLoader' => __DIR__.'/vendor/symfony/class-loader/',
        'Symfony\Component\Finder'      => __DIR__.'/vendor/symfony/finder/',
    ));

    $loader->register();
}
