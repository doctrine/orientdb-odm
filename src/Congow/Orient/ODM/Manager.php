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
use Congow\Orient\ODM\Mapper\Hydration\Result;
use Congow\Orient\Query;
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\Exception\ODM\OClass\NotFound as UnmappedClass;
use Congow\Orient\Query\Command\Select;
use Congow\Orient\Exception;
use Congow\Orient\Contract\Protocol\Adapter as ProtocolAdapter;
use Congow\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Congow\Orient\Validator\Rid as RidValidator;
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
     * This method should be used to executes query which should not return a
     * result (UPDATE, INSERT) or to retrieve multiple objects: to retrieve a
     * single record look at ->find*() methods.
     *
     * @param   Query $query
     * @return  Array 
     */
    public function execute(Query $query)
    {
        $adapter    = $this->getProtocolAdapter();
        $return     = $query->shouldReturn();
        $execution  = $adapter->execute($query->getRaw(), $return);
        $results    = $adapter->getResult();
        
        if ($execution) {
            if (is_array($results)) {
                $hydrationResults = $this->getMapper()->hydrateCollection($results);
                
                return $this->finalizeCollection($hydrationResults);
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
     * @param   string    $rid
     * @param   string    $fetchPlan
     * @return  Proxy|object
     * @throws  UnmappedClass|Exception
     */
    public function find($rid, $fetchPlan = null)
    {
        $validator  = new RidValidator;
        $rid        = $validator->check($rid);
        
        if ($fetchPlan === false) {
            return new Proxy($this, $rid);
        }
            
        try
        {
            return $this->doFind($rid, $fetchPlan);
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
     * returned Proxy object is called via __invoke.
     * @see     ->find()
     * @param   string      $rid
     * @param   mixed       $fetchPlan
     * @return  Proxy\Collection|array
     * @throws  Congow\Orient\Exception\Query\SQL\Invalid
     */
    public function findRecords(Array $rids, $fetchPlan = null)
    {
        if ($fetchPlan === false) {
            return new Proxy\Collection($this, $rids);
        }
        
        $query      = new Query($rids);
        $adapter    = $this->getProtocolAdapter();
        $execution  = $adapter->execute($query->getRaw(), true, $fetchPlan);
        
        if ($execution && $adapter->getResult()) {
            $collection = $this->getMapper()->hydrateCollection($adapter->getResult());
            
            return $this->finalizeCollection($collection);
        }

        return array();
    }
    
    /**
     * @todo to implement/test
     *
     * @param \stdClass $object 
     * @todo getter for documents
     */
    public function flush()
    {
        foreach ($this->documents as $document) {
            
            $annotation   = $this->getMapper()->getClassAnnotation(get_class($document));
            $orientClass  = $annotation->class;
            
            $propertyAnnotations = $this->getMapper()->getObjectPropertyAnnotations($document);
            
            $values = array();
            
            foreach ($propertyAnnotations as $property => $annotation) {
                $getter = 'get' . ucfirst($property);
                $values[$property] = $document->$getter();
                
            }
            

            
            $query        = new Query();
            $query->insert()
                  ->into($orientClass)
                  ->fields(array_keys($values))
                  ->values($values);
                  
            $this->execute($query);
            
        }
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
        return new Repository($className, $this, $this->getMapper());
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
        $this->documents[spl_object_hash($object)] = $object;
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
     * Executes a query against OrientDB, via the protocolAdapter, specifying
     * a $fetchPlan (which is optional) and a $rid to look for.
     * Then, it finalizes the hydration result.
     *
     * @param   type        $rid
     * @param   mixed       $fetchPlan
     * @return  object|null 
     */
    protected function doFind($rid, $fetchPlan = null)
    {
        $query      = new Query(array($rid));
        $adapter    = $this->getProtocolAdapter();
        $execution  = $adapter->execute($query->getRaw(), true, $fetchPlan);

        if ($execution && $result = $adapter->getResult()) {
          $record       = is_array($result) ? array_shift($result) : $result;
          $result       = $this->getMapper()->hydrate($record);

          return $this->finalize($result);
        }

        return null;
    }
    
    /**
     * Given an Hydration\Result, it implements lazy-loading for all its'
     * document's related links.
     *
     * @param   Result $result
     * @return  object
     */
    protected function finalize(Result $result)
    {
        foreach ($result->getLinkTracker()->getProperties() as $property => $value) {
            $setter = 'set' . ucfirst($property);
            $method = $value instanceof Rid\Collection ? 'findRecords' : 'find';
            $result->getDocument()->$setter($this->$method($value->getValue(), false));
        }
        
        return $result->getDocument();
    }
    
    /**
     * Given a collection of Hydration\Result, it returns an array of POPOs.
     *
     * @param   Array $collection
     * @return  Array
     */
    protected function finalizeCollection(Array $collection)
    {
        foreach ($collection as $key => $hydrationResult) {
            $collection[$key] = $this->finalize($hydrationResult);
        }

        return $collection;
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