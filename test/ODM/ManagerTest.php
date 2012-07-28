<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 * @version
 */

namespace test\ODM;

use test\PHPUnit\TestCase;
use Congow\Orient\Query;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper\LinkTracker;
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\ODM\Mapper\Hydration\Result as HydrationResult;

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

        $result = $this->getMock('Congow\Orient\Contract\Binding\BindingResultInterface');
        $result->expects($this->any())
               ->method('getResult')
               ->will($this->returnValue($rawResult));

        $binding = $this->getMock('Congow\Orient\Contract\Binding\BindingInterface');
        $binding->expects($this->any())
                ->method('execute')
                ->will($this->returnValue($result));

        $hydrationResultCallback = function ($document) {
            $linktracker = new LinkTracker();
            $linktracker->add('capital', new Rid('1:2'));

            return new HydrationResult(new Document\Stub\Contact\Address, $linktracker);
        };

        $mapper = $this->getMock('Congow\Orient\ODM\Mapper', array('hydrate'), array(__DIR__ . '/../../proxies'));
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

        $this->assertInstanceOf('Congow\Orient\ODM\Mapper\ClassMetadata', $metadata);
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
