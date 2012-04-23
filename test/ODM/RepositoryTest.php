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
use Congow\Orient\Query;
use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Repository;
use test\ODM\Document\Stub\Contact\Address;

class Stubadapter extends \Congow\Orient\Foundation\Protocol\Adapter\Http
{
    public function __construct(){}

    public function execute($query)
    {
        return array(0,1);
    }
}

class TestMapperForRepository extends Mapper
{

}

class TestManager extends \Congow\Orient\ODM\Manager
{
    public function find($rid, $lazy = false)
    {
        if($rid == '97:1') {
            return null;
        }
        
        return new Address;
    }

    public function execute(\Congow\Orient\Query $q)
    {
        return array(new Address);
    }
}

class RepositoryTest extends TestCase
{
    protected $repository;
    
    public function setup()
    {
        $mapper = new TestMapperForRepository('/');
        $this->repository = new Repository('test\ODM\Document\Stub\Contact\Address', new TestManager($mapper,new Stubadapter), $mapper);
    }
    
    public function testFindAll()
    {
        $documents = $this->repository->findAll();
        $this->assertEquals(2, count($documents));
    }
}