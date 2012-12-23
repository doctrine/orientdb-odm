<?php

/**
 * ManagerTest class
 *
 * @package
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Integration;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;

class ManagerTest extends TestCase
{
    public function testExecutionOfASelect()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $addresses = $manager->execute($query);

        $this->assertEquals(166, count($addresses));
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }

    public function testFindingARecordWithAnExecuteReturnsAnArrayHowever()
    {
        $manager = $this->createManager();

        $query = new Query(array('19:0'));
        $addresses = $manager->execute($query);

        $this->assertEquals(1, count($addresses));
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }

    public function testExecutionOfAnUpdate()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->update('Address')->set(array('my' => 'yours'))->where('@rid = ?', '1:10000');
        $result = $manager->execute($query);

        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Doctrine\OrientDB\Binding\InvalidQueryException
     */
    public function testAnExceptionGetsRaisedWhenExecutingAWrongQuery()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->update('Address')->set(array())->where('@rid = ?', '1:10000');

        $manager->execute($query);
    }

    public function testFindingARecord()
    {
        $manager = $this->createManager();
        $address = $manager->find('19:0');

        $this->assertInstanceOf("test\Integration\Document\Address", $address);
    }

    public function testFindingARecordWithAFetchPlan()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find('94:0', '*:-1');

        $this->assertInternalType('array', $post->comments);
        $this->assertFalse($post->comments instanceOf \Doctrine\OrientDB\ODM\Proxy\Collection);
    }


    public function testGettingARelatedRecord()
    {
        $manager = $this->createManager();
        $address = $manager->find('19:0');

        $this->assertInstanceOf("test\Integration\Document\Country", $address->getCity());
    }

    public function testGettingARelatedCollection()
    {
// $this->markTestIncomplete('$comments is not an array');
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find('94:0');
        $comments = $post->getComments();
        $comments = $comments();

        $this->assertInstanceOf("test\Integration\Document\Comment", $comments[0]);
    }

    /**
     * @expectedException Doctrine\ODM\OrientDB\OClassNotFoundException
     */
    public function testLookingForANonMappedTypeRaisesAnException()
    {
        $manager = $this->createManager(array(
            'document_dir' => array('./docs' => '\\'),
        ));

        $manager->find('19:0');
    }

    public function testFindingANonExistingRecord()
    {
        $manager = $this->createManager();

        $address = $manager->find('19:2000');

        $this->assertInternalType("null", $address);
    }

    public function testFindingSomeRecords()
    {
        $manager = $this->createManager();

        $addresses = $manager->findRecords(array('19:0', '19:1'));

        $this->assertEquals(2, count($addresses));
        $this->assertInstanceOf("test\Integration\Document\Address", $addresses[0]);
    }

    public function testFindingSomeGoodAndSomeWrongRecordsReturnsGoodRecords()
    {
        $manager = $this->createManager();
        $manager->findRecords(array('19:0', '19:700000'));
    }

    public function testExecutingASelectOfASingleRecordReturnsAnArrayWithOneRecord()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->where('@rid = ?', '19:0');

        $results = $manager->execute($query);

        $this->assertInternalType('array', $results);
        $this->assertSame(1, count($results));
    }

    public function testExecutionWithNoOutput()
    {
        $manager = $this->createManager();

        $query = new Query();
        $query->update('Address')->set(array('type' => 'Residence'));

        $results = $manager->execute($query);

        $this->assertInternalType('bool', $results);
        $this->assertTrue($results);
    }
}
