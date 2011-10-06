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
use Congow\Orient\Exception\ODM\OClass\NotFound as UnmappedClass;
use Congow\Orient\Query\Command\Select;
use Congow\Orient\Exception;
use Congow\Orient\Contract\Protocol\Adapter as ProtocolAdapter;
use Congow\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as MetadataFactory;

class Manager implements ObjectManager
{
    protected $mapper;
    protected $metadataFactory;
    protected $protocolAdapter;
    
    /**
     * Instatiates a new Mapper, injecting the $mapper that will be used to
     * hydrate record retrieved through the $protocolAdapter.
     * 
     * @param   Mapper          $mapper
     * @param   ProtocolAdapter $protocolAdapter
     * @param   MetadataFactory $metadataFactory
     * @todo    inject the metadata factory
     */
    public function __construct(
        Mapper $mapper, 
        ProtocolAdapter $protocolAdapter, 
        MetadataFactory $metadataFactory = null
    )
    {
        $this->mapper           = $mapper;
        $this->protocolAdapter  = $protocolAdapter;
        $this->metadataFactory  = $metadataFactory ?: new ClassMetadataFactory($this->getMapper());
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
     * Executes a $query against OrientDB.
     * This method should be used to retrieve multiple objects: to retrieve a
     * single record look at ->find*() methods.
     *
     * @param   Query $query
     * @return  Array 
     * @todo test what happens when executing a find here
     * @todo messy code
     */
    public function execute(Query $query)
    {
        $adapter    = $this->getProtocolAdapter();
        $return     = false;
        
        if ($query->getCommand() instanceOf Select) {
            $return = true;
        }
        
        $execution = $adapter->execute($query->getRaw(), $return);
        
        if ($execution) {
            if ($adapter->getResult()) {
                $collection = $this->getMapper()->hydrateCollection($adapter->getResult());
              
                foreach ($collection as $key => $partialObject) {
                    $document    = $partialObject[0];
                    $linkTracker = $partialObject[1];

                    foreach ($linkTracker->getProperties() as $property => $value) {
                        $method = 'set' . ucfirst($property);

                        $document->$method($this->find($value, true));
                    }

                    $collection[$key] = $document;
                }
            
                return $collection;
            }
            
            return true;
        }
        
        return false;
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
     * @todo throw custom exception | aware that orient gives an exception when it does not fiind the record with $rid
     * @todo messy code
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

            if ($adapter->execute($query->getRaw(), true) && $adapter->getResult()) {
              $record       = is_array($adapter->getResult()) ? array_shift($adapter->getResult()) : $adapter->getResult();
              $result       = $this->getMapper()->hydrate($record);
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
        catch (UnmappedClass $e) {
            throw $e;
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
     * @see     ->find()
     * @param   string    $rid
     * @param   boolean   $lazy
     * @return  Proxy\Collection|array
     * @todo duplicated logic to hydrate partial results (here and in find() method)
     * @throws Congow\Orient\Exception\Query\SQL\Invalid
     * @todo throw specific exception "You are trying to retrieve 11:0, 11:1 but some of these are out of cluster size..."
     */
    public function findRecords(Array $rids, $lazy = false)
    {
        if ($lazy) {
            return new Proxy\Collection($this, $rids);
        }
        
        $query      = new Query($rids);
        $adapter    = $this->getProtocolAdapter();

        if ($adapter->execute($query->getRaw(), true) && $adapter->getResult()) {

            $collection = $this->getMapper()->hydrateCollection($adapter->getResult());

            foreach ($collection as $key => $partialObject) {
                $document    = $partialObject[0];
                $linkTracker = $partialObject[1];

                foreach ($linkTracker->getProperties() as $property => $value) {
                    $method = 'set' . ucfirst($property);

                    $document->$method($this->find($value, true));
                }

                $collection[$key] = $document;
            }

            return $collection;
        }

        return array();
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
     * Gets the $class Metadata.
     *
     * @param   string $class
     * @return  Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata($class)
    {
        return $this->getMetadataFactory()->getMetadataFor($class);
    }
    
    /**
     * Returns the Metadata factory associated with this manager.
     *
     * @return MetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }
    
    /**
     * Returns the Repository class associated with the $class.
     *
     * @param   string $className
     * @return  Repository
     */
    public function getRepository($className)
    {
        return new Repository($className);
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
     * Returns the mapper of the current object.
     *
     * @return Mapper
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