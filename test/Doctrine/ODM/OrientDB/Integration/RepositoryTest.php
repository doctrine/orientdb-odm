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

/**
 * @group integration
 */
class RepositoryTest extends TestCase
{
    public $postId;
    public $addressId;

    public function setup()
    {
        $this->postId    = $this->getClassId('Post');
        $this->addressId = $this->getClassId('Address');
    }

    protected function createRepository($class)
    {
        $manager = $this->createManager(array(
            'mismatches_tolerance' => true,
        ));

        $repository = $manager->getRepository($class);

        return $repository;
    }

    public function testFindingADocumentOfTheRepo()
    {
        $class = 'test\Integration\Document\Post';
        $repository = $this->createRepository($class);

        $this->assertInstanceOf($class, $repository->find($this->postId.':0'));
    }

    public function testYouCanSpecifyFetchplansWithTheRepo()
    {
        $class = 'test\Integration\Document\Post';
        $repository = $this->createRepository($class);

        $this->assertInstanceOf(static::COLLECTION_CLASS, $repository->find($this->postId . ':0', '*:0')->comments);
        $this->assertInstanceOf(static::COLLECTION_CLASS, $repository->find($this->postId . ':0', '*:-1')->comments);
    }

    /**
     * @expectedException \Doctrine\OrientDB\Exception
     */
    public function testFindingADocumentOfAnotherRepoRaisesAnException()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');
        $repository->find($this->addressId.':0');
    }

    public function testFindingANonExistingDocument()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');

        $this->assertNull($repository->find('27:985023989'));
    }

    public function testRetrievingAllTheRepo()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');
        $posts = $repository->findAll();

        $this->assertEquals(7, count($posts));
    }

    public function testRetrievingByCriteria()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');

        $posts = $repository->findBy(array('title' => 'aaaa'), array('@rid' => 'DESC'));
        $this->assertCount(0, $posts);

        $posts = $repository->findBy(array(), array('@rid' => 'DESC'));
        $this->assertCount(7, $posts);
        $this->assertTrue($posts[0]->getRid() > $posts[1]->getRid());

        $posts = $repository->findBy(array(), array('@rid' => 'ASC'));
        $this->assertCount(7, $posts);
        $this->assertTrue($posts[0]->getRid() < $posts[1]->getRid());

        $posts = $repository->findBy(array(), array('@rid' => 'ASC'), 1);
        $this->assertCount(1, $posts);
    }

    public function testRetrievingARecordByCriteria()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');

        $post = $repository->findOneBy(array('title' => 0), array('@rid' => 'DESC'));
        $this->assertInstanceOf("test\Integration\Document\Post", $post);
    }
}
