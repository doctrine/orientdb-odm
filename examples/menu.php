<?php

namespace Doctrine\Orient;

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Orient\Binding\HttpBinding;
use Doctrine\Orient\Binding\BindingParameters;

require __DIR__.'/../autoload.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Domain' => __DIR__.'/../examples/'));
$loader->register();

$parameters = BindingParameters::create('http://admin:admin@127.0.0.1:2480/menu');
$binding = new HttpBinding($parameters);

$mapper = new ODM\Mapper(__DIR__ . '/../proxies');
$mapper->setDocumentDirectories(array(__DIR__.'/../examples/' => 'Domain'));

$manager = new ODM\Manager($mapper, $binding);
$menus = $manager->getRepository('Domain\Menu');

foreach ($menus->findAll() as $menu) {
    echo "Menu: ", $menu->getTitle(), "\n";

    foreach ($menu->getLinks() as $link) { // object inheriting from Link
        echo "Link \"{$link->getTitle()}\" ====>>> {$link->getLink()}\n";
    }
}
