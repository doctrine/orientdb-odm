<?php

namespace Doctrine\OrientDB;

use Symfony\Component\ClassLoader\UniversalClassLoader;

require __DIR__.'/../autoload.php';
require __DIR__.'/OrientDB-PHP/OrientDB/OrientDB.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Domain' => __DIR__.'/../examples/'));
$loader->registerNamespaces(array('Doctrine\OrientDB\Proxy' => __DIR__.'/../proxies/'));
$loader->register();

class Binary implements Contract\Protocol\Adapter
{
    public function __construct()
    {
        $this->orient = new \OrientDB('localhost', 2424);
        $this->orient->connect('admin', 'admin');
        $this->orient->DBOpen('demo', 'admin', 'admin');
    }

    public function execute($sql)
    {
        $result = $this->orient->select($sql);

        $results = array();

        foreach ($result as $record) {
            $record->parse();
            $results[] = (object) array('@class' => $record->className, 'street' => $record->data->street);
        }

        $this->result = $results;

        return $results;
    }

    public function getResult()
    {
        return $this->result;
    }
}


$client          = new Http\Client\Curl();
$protocolAdapter = new Binary();
$mapper          = new ODM\Mapper(__DIR__ . '/../proxies');
$mapper->setDocumentDirectories(array(__DIR__.'/../examples/' => 'Domain'));
$manager         = new ODM\Manager($mapper, $protocolAdapter);

$addresses = $manager->getRepository('Domain\Address');

foreach ($addresses->findAll() as $address)
{
    echo "Address: " . $address->street . "\n";
}


/**
 *
 *
odino@brigitta:~/projects/Orient$ php examples/binary.php
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Piazza Navona, 1
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Plaza central
Address: Rio de Castilla
Address: Piazza di Spagna, 111
Address: Piazza Navona, 1
Address: unknown
Address: unknown
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
Address: WA 98073-9717
odino@brigitta:~/projects/Orient$
 */
