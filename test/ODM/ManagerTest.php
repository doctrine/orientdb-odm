<?php

/**
 * QueryTest
 *
 * @package    Doctrine\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 * @version
 */

namespace test\ODM;

use test\PHPUnit\TestCase;
use Doctrine\Orient\Query;
use Doctrine\Orient\ODM\Manager;
use Doctrine\Orient\ODM\Mapper\LinkTracker;
use Doctrine\Orient\Foundation\Types\Rid;
use Doctrine\Orient\ODM\Mapper\Hydration\Result as HydrationResult;

class ManagerTest extends TestCase
{
    protected function createTestManager()
    {
        $rawResult = json_decode('[{
            "@type": "d", "@rid": "#19:0", "@version": 2, "@class": "Address",
            "name": "Luca",
            "surname": "Garulli",
            "out": ["#20:1"]
        }]');

        $result = $this->getMock('Doctrine\Orient\Contract\Binding\BindingResultInterface');
        $result->expects($this->any())
               ->method('getResult')
               ->will($this->returnValue($rawResult));

        $binding = $this->getMock('Doctrine\Orient\Contract\Binding\BindingInterface');
        $binding->expects($this->any())
                ->method('execute')
                ->will($this->returnValue($result));

        $hydrationResultCallback = function ($document) {
            $linktracker = new LinkTracker();
            $linktracker->add('capital', new Rid('1:2'));

            return new HydrationResult(new Document\Stub\Contact\Address, $linktracker);
        };

        $mapper = $this->getMock('Doctrine\Orient\ODM\Mapper', array('hydrate'), array(__DIR__ . '/../../proxies'));
        $mapper->expects($this->any())
               ->method('hydrate')
               ->will($this->returnCallback($hydrationResultCallback));

        $manager = new Manager($mapper, $binding);

        return $manager;
    }

    public function testMethodUsedToTryTheManager()
    {
        $manager = $this->createTestManager();
        $metadata = $manager->getClassMetadata('test\ODM\Document\Stub\Contact\Address');

        $this->assertInstanceOf('Doctrine\Orient\ODM\Mapper\ClassMetadata', $metadata);
    }

    public function testManagerActsAsAProxyForExecutingQueries()
    {
        $query = new Query(array('Address'));
        $manager = $this->createTestManager();
        $results = $manager->execute($query);

        $this->assertInternalType('array', $results);
        $this->assertInstanceOf('test\ODM\Document\Stub\Contact\Address', $results[0]);
    }

    public function testFindingADocument()
    {
        $manager = $this->createTestManager();

        $this->assertInstanceOf('test\ODM\Document\Stub\Contact\Address', $manager->find('1:1'));
    }
}
