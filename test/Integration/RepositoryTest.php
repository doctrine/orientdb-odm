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
use Congow\Orient\Query;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Repository;

class RepositoryTest extends TestCase
{
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

        $this->assertInstanceOf($class, $repository->find('30:0'));
    }

    /**
     * @expectedException Congow\Orient\Exception
     */
    public function testFindingADocumentOfAnotherRepoRaisesAnException()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');
        $repository->find('13:0');
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

        $this->assertEquals(2, count($posts));
    }

    public function testRetrievingByCriteria()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');

        $posts = $repository->findBy(array('title' => 'aaaa'), array('@rid' => 'DESC'));
        $this->assertCount(0, $posts);

        $posts = $repository->findBy(array(), array('@rid' => 'DESC'));
        $this->assertCount(2, $posts);
        $this->assertTrue($posts[0]->getRid() > $posts[1]->getRid());

        $posts = $repository->findBy(array(), array('@rid' => 'ASC'));
        $this->assertCount(2, $posts);
        $this->assertTrue($posts[0]->getRid() < $posts[1]->getRid());

        $posts = $repository->findBy(array(), array('@rid' => 'ASC'), 1);
        $this->assertCount(1, $posts);
    }

    public function testRetrievingARecordByCriteria()
    {
        $repository = $this->createRepository('test\Integration\Document\Post');

        $post = $repository->findOneBy(array('title' => 'aaaa'), array('@rid' => 'DESC'));
        $this->assertNull(null, $post);

        $post = $repository->findOneBy(array());
        $this->assertInstanceOf("test\Integration\Document\Post", $post);

        $post = $repository->findOneBy(array());
        $this->assertInstanceOf("test\Integration\Document\Post", $post);

        $post = $repository->findOneBy(array());
        $this->assertInstanceOf("test\Integration\Document\Post", $post);
    }
}
