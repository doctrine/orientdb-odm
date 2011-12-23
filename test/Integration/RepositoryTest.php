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
    public function setup()
    {
        $mapper          = new Mapper(__DIR__ . "/../../proxies");
        $mapper->setDocumentDirectories(array('./test/Integration/Document' => 'test'));
        $client          = new \Congow\Orient\Http\Client\Curl(false, 10);
        $binding         = new \Congow\Orient\Foundation\Binding($client, TEST_ODB_HOST, TEST_ODB_PORT, TEST_ODB_USER, TEST_ODB_PASSWORD, TEST_ODB_DATABASE);
        $protocolAdapter = new \Congow\Orient\Foundation\Protocol\Adapter\Http($binding);
        $manager         = new Manager($mapper, $protocolAdapter);
        
        $this->repository = new Repository("test\Integration\Document\Post", $manager, $mapper);
    }
    
    public function testFindingADocumentOfTheRepo()
    {
        $post = $this->repository->find('30:0');
        
        $this->assertInstanceOf("test\Integration\Document\Post", $post);
    }
    
    /**
     * @expectedException Congow\Orient\Exception
     */
    public function testFindingADocumentOfAnotherRepoRaisesAnException()
    {
        $post = $this->repository->find('13:0');
        
        $this->assertInstanceOf("test\Integration\Document\Post", $post);
    }
    
    public function testFindingANonExistingDocument()
    {
        $post = $this->repository->find('27:985023989');
        
        $this->assertInternalType('null', $post);
    }
    
    public function testRetrievingAllTheRepo()
    {
        $posts = $this->repository->findAll();
        
        $this->assertEquals(2, count($posts));
    }

    public function testRetrievingByCriteria()
    {
        $criteria = array(
          'title' => 'aaaa'
        );
        $posts = $this->repository->findBy($criteria, array('@rid' => 'DESC'));
        
        $this->assertEquals(0, count($posts));
        
        $posts = $this->repository->findBy(array(), array('@rid' => 'DESC'));
        
        $this->assertTrue($posts[0]->getRid() > $posts[1]->getRid());

        $posts = $this->repository->findBy(array(), array('@rid' => 'ASC'));
        
        $this->assertTrue($posts[0]->getRid() < $posts[1]->getRid());

        $posts = $this->repository->findBy(array(), array('@rid' => 'ASC'), 1);
        
        $this->assertEquals(1, count($posts));
    }

    public function testRetrievingARecordByCriteria()
    {
        $criteria = array(
          'title' => 'aaaa'
        );
        $post = $this->repository->findOneBy($criteria, array('@rid' => 'DESC'));
        
        $this->assertEquals(null, $post);
        
        $post = $this->repository->findOneBy(array());
        
        $this->assertInstanceOf("test\Integration\Document\Post", $post);

        $post = $this->repository->findOneBy(array());
        
        $this->assertInstanceOf("test\Integration\Document\Post", $post);

        $post = $this->repository->findOneBy(array());
        
        $this->assertInstanceOf("test\Integration\Document\Post", $post);
    }
}