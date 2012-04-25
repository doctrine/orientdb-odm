<?php

/**
 * ReporitoryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test;

use test\PHPUnit\TestCase;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Repository;
use Congow\Orient\ODM\Mapper\LinkTracker;
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\ODM\Mapper\Hydration\Result as HydrationResult;
use test\ODM\Document\Stub\Contact\Address;

class RepositoryTest extends TestCase
{
    protected function createRepository()
    {
        $rawResult = json_decode('[{
            "@type": "d", "@rid": "#19:1", "@version": 1, "@class": "Address",
            "name": "Luca",
            "surname": "Garulli",
            "out": ["#20:1"]
        }, {
            "@type": "d", "@rid": "#19:1", "@version": 1, "@class": "Address",
            "name": "Luca",
            "surname": "Garulli",
            "out": ["#20:1"]
        }]');

        $result = $this->getMock('Congow\Orient\Contract\Binding\BindingResultInterface');
        $result->expects($this->at(0))
               ->method('getResult')
               ->will($this->returnValue($rawResult));
        $result->expects($this->at(1))
               ->method('getResult')
               ->will($this->returnValue(array()));

        $binding = $this->getMock('Congow\Orient\Contract\Binding\BindingInterface');
        $binding->expects($this->any())
                ->method('execute')
                ->will($this->returnValue($result));


        $hydrationResultCallback = function($document) {
            $linktracker = new LinkTracker();
            $linktracker->add('capital', new Rid('20:1'));

            return new HydrationResult(new Address(), $linktracker);
        };

        $mapper = $this->getMock('Congow\Orient\ODM\Mapper', array('hydrate'), array(__DIR__ . '/../../proxies'));
        $mapper->expects($this->any())
               ->method('hydrate')
               ->will($this->returnCallback($hydrationResultCallback));

        $manager = new Manager($mapper, $binding);

        $repository = new Repository('test\ODM\Document\Stub\Contact\Address', $manager, $mapper);

        return $repository;
    }

    public function testFindAll()
    {
        $repository = $this->createRepository();
        $documents = $repository->findAll();

        $this->assertSame(2, count($documents));
    }
}
