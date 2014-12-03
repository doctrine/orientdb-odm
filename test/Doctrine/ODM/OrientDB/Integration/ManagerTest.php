<?php

/**
 * ManagerTest class
 *
 * @package    Doctrine\ODM\OrientDB
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Doctrine\ODM\OrientDB\Integration;

use test\PHPUnit\TestCase;
use Doctrine\OrientDB\Query\Query;

/**
 * @group integration
 */
class ManagerTest extends TestCase
{
    public $postId;
    public $addressId;

    public function setup()
    {
        $this->postId    = $this->getClassId('Post');
        $this->addressId = $this->getClassId('Address');
    }

    /**
     * @group integration
     */
    public function testGettingARelatedCollectionViaLazyLoading()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post       = $manager->find($this->postId.':0', '*:0');
        $comments   = $post->getComments();

        $this->assertInstanceOf('test\Integration\Document\Comment', $comments[0]);
    }

    /**
     * @group integration
     */
    public function testExecutionOfASelect()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $addresses = $manager->execute($query);

        $this->assertEquals(40, count($addresses));
        $this->assertInstanceOf('test\Integration\Document\Address', $addresses[0]);
    }

    /**
     * @group integration
     */
    public function testFindingARecordWithAnExecuteReturnsAnArrayHowever()
    {
        $manager = $this->createManager();

        $query = new Query(array($this->addressId.':0'));
        $addresses = $manager->execute($query);

        $this->assertEquals(1, count($addresses));
        $this->assertInstanceOf('test\Integration\Document\Address', $addresses[0]);
    }

    /**
     * @group integration
     */
    public function testExecutionOfAnUpdate()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->update('Address')->set(array('my' => 'yours'))->where('@rid = ?', $this->addressId.':30');
        $result = $manager->execute($query);

        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
    }

    /**
     * @group integration
     * @expectedException \Doctrine\OrientDB\Binding\InvalidQueryException
     */
    public function testAnExceptionGetsRaisedWhenExecutingAWrongQuery()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->update('Address')->set(array())->where('@rid = ?', '1:10000');

        $manager->execute($query);
    }

    /**
     * @group integration
     */
    public function testFindingARecord()
    {
        $manager = $this->createManager();
        $address = $manager->find($this->addressId.':0');

        $this->assertInstanceOf('test\Integration\Document\Address', $address);
    }

    /**
     * @group integration
     */
    public function testFindingARecordWithAFetchPlan()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post = $manager->find($this->postId.':0', '*:-1');

        $this->assertInstanceOf(static::COLLECTION_CLASS, $post->comments);
    }

    /**
     * @group integration
     */
    public function testGettingARelatedRecord()
    {
        $manager = $this->createManager();
        $address = $manager->find($this->addressId.':0');

        $this->assertInstanceOf('test\Integration\Document\Country', $address->getCity());
    }

    /**
     * @group integration
     */
    public function testGettingARelatedCollection()
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $post       = $manager->find($this->postId.':0');
        $comments   = $post->getComments();

        $this->assertInstanceOf('test\Integration\Document\Comment', $comments[0]);
    }

    /**
     * @group integration
     * @expectedException \Doctrine\ODM\OrientDB\OClassNotFoundException
     */
    public function testLookingForANonMappedTypeRaisesAnException()
    {
        $manager = $this->createManager(array(
            'document_dirs' => array('./docs' => '\\'),
        ));

        $manager->find($this->postId.':0');
    }

    /**
     * @group integration
     */
    public function testFindingANonExistingRecord()
    {
        $manager = $this->createManager();

        $address = $manager->find($this->postId.':2000');

        $this->assertInternalType("null", $address);
    }

    /**
     * @group integration
     */
    public function testFindingSomeRecords()
    {
        $manager = $this->createManager();

        $addresses = $manager->findRecords(array($this->addressId.':0', $this->addressId.':1'));

        $this->assertEquals(2, count($addresses));
        $this->assertInstanceOf('test\Integration\Document\Address', $addresses[0]);
    }

    /**
     * @group integration
     */
    public function testFindingSomeGoodAndSomeWrongRecordsReturnsGoodRecords()
    {
        $manager = $this->createManager();
        $manager->findRecords(array($this->postId.':0', $this->postId.':700000'));
    }

    /**
     * @group integration
     */
    public function testExecutingASelectOfASingleRecordReturnsAnArrayWithOneRecord()
    {
        $manager = $this->createManager();

        $query = new Query(array('Address'));
        $query->where('@rid = ?', $this->addressId.':0');

        $results = $manager->execute($query);

        $this->assertInstanceOf(static::COLLECTION_CLASS, $results);
        $this->assertSame(1, count($results));
    }

    /**
     * @group integration
     */
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
