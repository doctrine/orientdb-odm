<?php

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\OrientDB\Binding\HttpBinding;
use Doctrine\OrientDB\Binding\BindingParameters;
use Doctrine\ODM\OrientDB as ODM;

require __DIR__.'/../autoload.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Domain' => __DIR__.'/../examples/'));
$loader->register();

$parameters = BindingParameters::create('http://admin:admin@127.0.0.1:2480/menu');
$binding = new HttpBinding($parameters);

$configuration = new ODM\Configuration(array(
    'document_dirs' => array(__DIR__.'/../examples/' => 'Domain'),
    'proxy_dir' => __DIR__ . '/../examples/proxies'
));


$manager = new ODM\Manager($binding, $configuration);
$menus = $manager->getRepository('Domain\Menu');

foreach ($menus->findAll() as $menu) {
    echo "Menu: ", $menu->getTitle(), "\n";

    foreach ($menu->getLinks() as $link) { // object inheriting from Link
        echo "Link \"{$link->getTitle()}\" ====>>> {$link->getLink()}\n";
    }
}
