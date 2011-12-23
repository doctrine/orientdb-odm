<?php

namespace Congow\Orient;

use Symfony\Component\ClassLoader\UniversalClassLoader;

require __DIR__.'/../autoload.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Domain' => __DIR__.'/../examples/'));
$loader->register();


$client             = new Http\Client\Curl();
$binding            = new Foundation\Binding($client, '127.0.0.1', 2480, 'admin', 'admin', 'menu');
$protocolAdapter    = new Foundation\Protocol\Adapter\Http($binding);
$mapper             = new ODM\Mapper(__DIR__ . '/../proxies');
$mapper->setDocumentDirectories(array(__DIR__.'/../examples/' => 'Domain'));
$manager            = new ODM\Manager($mapper, $protocolAdapter);

$menus = $manager->getRepository('Domain\Menu');

foreach ($menus->findAll() as $menu)
{
    echo "Menu: " . $menu->getTitle() . "\n";

    foreach ($menu->getLinks() as $link) // object inheriting from Link
    {
        echo "Link \"{$link->getTitle()}\" ====>>> {$link->getLink()}\n";
    }
}
