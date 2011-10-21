<?php

namespace Congow\Orient;

require 'test/PHPUnit/bootstrap.php';

$classLoader = new \SplClassLoader('examples\Domain', __DIR__ . '/../');
$classLoader->register();



$client             = new Http\Client\Curl();
$binding            = new Foundation\Binding($client, '127.0.0.1', 2480, 'admin', 'admin', 'menu');
$protocolAdapter    = new Foundation\Protocol\Adapter\Http($binding);
$mapper             = new ODM\Mapper(__DIR__ . '/../proxies');
$mapper->setDocumentDirectories(array('./examples/Domain' => '\\'));
$manager            = new ODM\Manager($mapper, $protocolAdapter);

$menus = $manager->getRepository('examples\Domain\Menu');

foreach ($menus->findAll() as $menu)
{
    echo "Menu: " . $menu->getTitle() . "\n";
    
    foreach ($menu->getLinks() as $link) // object inheriting from Link
    {
        echo "Link \"{$link->getTitle()}\" ====>>> {$link->getLink()}\n";
    }
}
