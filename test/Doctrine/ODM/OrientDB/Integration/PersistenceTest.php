<?php

namespace test\Doctrine\ODM\OrientDB\Integration;


use test\Integration\Document\Country;
use test\PHPUnit\TestCase;

class PersistenceTest extends TestCase
{
    protected $manager;

    protected function setUp()
    {
        $this->manager = $this->createManager();
    }

    public function testPersistSingleDocument()
    {
        $document       = new Country();
        $document->name = 'SinglePersistTest';

        $this->manager->persist($document);
        $this->manager->flush();

        $this->assertNotNull($document->rid);

        $proxy = $this->manager->find($document->rid);
        $this->assertEquals('SinglePersistTest', $proxy->name);
    }
} 