<?php

/**
 * QueryTest
 *
 * @package    Congow\Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @version
 */

namespace test\ODM;

use test\PHPUnit\TestCase;
use Congow\Orient\Query;
use Congow\Orient\ODM\Manager;

class TestMapper extends \Congow\Orient\ODM\Mapper
{    
    public function hydrate($document)
    {
        $linktracker = new \Congow\Orient\ODM\Mapper\LinkTracker;
        $linktracker->add('capital', new Document\Stub\Contact\Address);
        
        return array(
          new Document\Stub\Contact\Address, $linktracker
        );
    }
    
    public function getDocumentDirectories()
    {
        return 'dir';
    }
    
    protected function findClassMappingInDirectories($OClass)
    {
        return "Document\Stub\Contact\Address";
    }
}

class TestAdapter extends \Congow\Orient\Foundation\Protocol\Adapter\Http
{
    public function __construct()
    {

    }
    
    public function execute($sql)
    {
        return 'query';
    }
    
    public function getResult()
    {
        $record = json_decode('{
                    "@type": "d", "@rid": "#19:0", "@version": 2, "@class": "Address", 
                    "name": "Luca", 
                    "surname": "Garulli", 
                    "out": ["#20:1"]
        }');
        
        return $record;
    }
}

class ManagerTest extends TestCase
{
    public function setup()
    {
        $this->manager = new Manager(new TestMapper(__DIR__ . "/../../proxies"), new TestAdapter());
    }
    
    public function testMethodUsedToTryTheManager()
    {
        $metadata = $this->manager->getClassMetadata("test\ODM\Document\Stub\Contact\Address");
        $this->assertInstanceOf('\Congow\Orient\ODM\Mapper\ClassMetadata', $metadata);
    }
    
    public function managerActsAsAProxyForDocumentDirectories()
    {
        $this->assertInstanceOf('dir', $this->manager->getDocumentDirectories());
    }
    
    public function managerActsAsAProxyForExecutingQueries()
    {
        $this->assertInstanceOf('query', $this->manager->execute(new Query));
    }
    
    public function testFindingADocument()
    {
        $this->assertInstanceOf("test\ODM\Document\Stub\Contact\Address", $this->manager->find('1:1'));
    }
}