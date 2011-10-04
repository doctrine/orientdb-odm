<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * (c) David Funaro <ing.davidino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Manager class.
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\ODM\Mapper;
use Congow\Orient\Query;
use Congow\Orient\Exception;
use Congow\Orient\Contract\Protocol\Adapter as ProtocolAdapter;
use Congow\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;

class Manager implements ObjectManager
{
    protected $mapper;
    protected $metadataFactory;
    protected $protocolAdapter;
    
    /**
     * @param   Mapper $mapper
     * @todo    inject the metadata factory
     */
    public function __construct(Mapper $mapper, ProtocolAdapter $protocolAdapter)
    {
        $this->mapper           = $mapper;
        $this->protocolAdapter  = $protocolAdapter;
        $this->metadataFactory  = new ClassMetadataFactory($this->getMapper());
    }
    
    /**
     * get the document directories paths
     * @return Array 
     */
    public function getDocumentDirectories()
    {
        return $this->getMapper()->getDocumentDirectories();
    }
    
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function detach($object)
    {
        throw new \Exception();
    }
    
    /**
     * @todo phpdoc
     */
    public function execute(Query $query)
    {
        return $this->getProtocolAdapter()->execute($query->getRaw());
    }

    /**
     * Via a protocol adapter, it queries for an object with the given $rid.
     * If $lazy loading is used, all of this won't be executed unless the
     * returned Proxy object is called via __invoke, e.g.:
     * 
     * <code>
     *   $lazyLoadedRecord = $manager->find('1:1', true);
     * 
     *   $record = $lazyLoadedRecord();
     * </code>
     *
     * @param string    $rid
     * @param boolean   $lazy
     * @return Proxy|object
     * @todo wrap the returning array as an object (Hydration\Result? PartialObject?)
     */
    public function find($rid, $lazy = false)
    {
        if ($lazy) {
            return new Proxy($this, $rid);
        }
        
        try
        {
            $query      = new Query(array($rid));
            $adapter    = $this->getProtocolAdapter();

            if ($adapter->execute($query->getRaw()) && $adapter->getResult()) {
              $result = $this->getMapper()->hydrate($adapter->getResult());
              $document    = $result[0];
              $linkTracker = $result[1];
              
              foreach ($linkTracker->getProperties() as $property => $value) {
                  $method = 'set' . ucfirst($property);
                  
                  $document->$method($this->find($value, true));
              }
              
              return $document;
            }
            
            return null;
        }
        catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Via a protocol adapter, it queries for an array of objects with the given
     * $rids.
     * If $lazy loading is used, all of this won't be executed unless the
     * returned Proxy object is called via __invoke, e.g.:
     * 
     * <code>
     *   $lazyLoadedRecords = $manager->find('1:1', true);
     * 
     *   $records = $lazyLoadedRecord();
     * </code>
     *
     * @param string    $rid
     * @param boolean   $lazy
     * @return Proxy\Collection|array
     * @todo duplicated logic to hydrate partial results (here and in find() method)
     */
    public function findRecords(Array $rids, $lazy = false)
    {
        if ($lazy) {
            return new Proxy\Collection($this, $rids);
        }
        
        $collection = $this->getMapper()->hydrateCollection($this->getProtocolAdapter()->findRecords($rids));
        
        foreach ($collection as $key => $partialObject) {
            $document    = $partialObject[0];
            $linkTracker = $partialObject[1];
            
            foreach ($linkTracker->getProperties() as $property => $value) {
                $method = 'set' . ucfirst($property);
                
                $document->$method($this->find($value, true));
            }
            
            $collection[$key] = $document;
        }
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function flush()
    {
        throw new \Exception;
    }
    
    /**
     *
     * @todo phpdoc
     * @todo test
     */
    public function getClassMetadata($class)
    {
        return $this->getMetadataFactory()->getMetadataFor($class);
    }
    
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function getRepository($classname)
    {
        throw new \Exception;
    }    
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function merge($object)
    {
        throw new \Exception();
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function persist($object)
    {
        throw new \Exception();
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function remove($object)
    {
        throw new \Exception();
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     */
    public function refresh($object)
    {
        throw new \Exception();
    }
    
     /**
     * @todo phpdoc
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
    
    /**
     * Returns the protocol adapter used to communicate with a OrientDB
     * binding.
     *
     * @return Adapter
     */
    protected function getProtocolAdapter()
    {
        return $this->protocolAdapter;
    }
}