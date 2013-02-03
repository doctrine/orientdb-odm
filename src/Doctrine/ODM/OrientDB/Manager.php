<?php

/*
 * This file is part of the Doctrine\OrientDB package.
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
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\ODM\OrientDB;

use Doctrine\Common\EventManager;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\ODM\OrientDB\Mapper\Hydration\Result;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\ODM\OrientDB\Caster\CastingMismatchException;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Doctrine\OrientDB\Exception;
use Doctrine\OrientDB\Binding\BindingInterface;
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Query\Command\Select;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as MetadataFactory;

class Manager implements ObjectManager
{
    protected $mapper;
    protected $binding;
    protected $metadataFactory;

    /**
     * The DocumentRepository instances.
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository[]
     */
    private $repositories = array();

    /**
     * Instatiates a new Mapper, injecting the $mapper that will be used to
     * hydrate record retrieved through the $binding.
     *
     * @param   Mapper           $mapper
     * @param   BindingInterface $binding
     * @param   MetadataFactory  $metadataFactory
     * @param   EventManager     $eventManager
     */
    public function __construct(Mapper $mapper, BindingInterface $binding, MetadataFactory $metadataFactory = null, EventManager $eventManager = null)
    {
        $this->mapper = $mapper;
        $this->binding = $binding;
        $this->metadataFactory = $metadataFactory ?: new ClassMetadataFactory($mapper);
        $this->eventManager = $eventManager ?: new EventManager();
    }

    /**
     * Gets the EventManager used by the DocumentManager.
     *
     * @return EventManager
     */
    public function getEventManager()
    {
        return $this->eventManager;
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
     *
     * This method should be used to executes query which should not return a
     * result (UPDATE, INSERT) or to retrieve multiple objects: to retrieve a
     * single record look at ->find*() methods.
     *
     * @param   Query $query
     * @return  Array
     */
    public function execute(Query $query, $fetchPlan = null)
    {
        
        $binding = $this->getBinding();
        $results = $binding->execute($query->getRaw(), $query->shouldReturn(), $fetchPlan)->getResult();

        if (is_array($results)) {
            $collection = $this->getMapper()->hydrateCollection($results);
            $collection = $this->finalizeCollection($collection);
            
            return $collection;
        }

        return true;
    }

    /**
     * Queries for an object with the given $rid.
     *
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
     * @throws  OClassNotFoundException|CastingMismatchException|Exception
     */
    public function find($rid, $fetchPlan = '*:1', $lazy = true)
    {
        $validator = new RidValidator;
        $rid = $validator->check($rid);

        if ($lazy === false) {
            return new Proxy($this, $rid);
        }

        try {
            return $this->doFind($rid, $fetchPlan);
        } catch (OClassNotFoundException $e) {
            throw $e;
        } catch (CastingMismatchException $e) {
            throw $e;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Queries for an array of objects with the given $rids.
     *
     * If $lazy loading is used, all of this won't be executed unless the
     * returned Proxy object is called via __invoke.
     *
     * @see     ->find()
     * @param   string      $rid
     * @param   mixed       $fetchPlan
     * @return  Proxy\Collection|array
     * @throws  \Doctrine\OrientDB\Binding\InvalidQueryException
     */
    public function findRecords(Array $rids, $fetchPlan = null, $lazy = true)
    {
        if ($lazy === false) {
            return new Proxy\Collection($this, $rids);
        }

        $query = new Query($rids);
        $binding = $this->getBinding();
        $results = $binding->execute($query->getRaw(), true, $fetchPlan)->getResult();

        if (is_array($results)) {
            $collection = $this->getMapper()->hydrateCollection($results);
            $collection = $this->finalizeCollection($collection);

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
     * @return  \Doctrine\ODM\OrientDb\Mapper\ClassMetadata
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
        if (isset($this->repositories[$className])) {
            return $this->repositories[$className];
        }
        $metadata = $this->getClassMetadata($className);
        $customRepositoryClassName = $metadata->getRepositoryClassname();

        if (null !== $customRepositoryClassName) {
            $repository = new $customRepositoryClassName($className, $this, $this->getMapper(), $this->getEventManager());
        } else {
            $repository = new Repository($className, $this, $this->getMapper(), $this->getEventManager());
        }

        $this->repositories[$className] = $repository;
        return $repository;
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     * @todo implement and test
     */
    public function initializeObject($obj)
    {
        throw new \Exception();
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
     * @todo to implement/test
     *
     * @param \stdClass $object
     */
    public function clear($object = null)
    {
        throw new \Exception();
    }

    /**
     * @todo to implement/test
     *
     * @param \stdClass $object
     */
    public function contains($object)
    {
        throw new \Exception();
    }

    /**
     * Executes a query against OrientDB to find the specified RID and finalizes the
     * hydration result.
     *
     * Optionally the query can be executed using the specified fetch plan.
     *
     * @param   type        $rid
     * @param   mixed       $fetchPlan
     * @return  object|null
     */
    protected function doFind($rid, $fetchPlan = null)
    {
        $query      = new Query(array($rid));
        $binding    = $this->getBinding(); 
        $results    = $binding->execute($query->getRaw(), true, $fetchPlan)->getResult();

        if (isset($results) && count($results)) {
          $record = is_array($results) ? array_shift($results) : $results;
          $results = $this->getMapper()->hydrate($record);

          return $this->finalize($results);
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
            
            if ($value instanceOf Rid\Collection || $value instanceOf Rid) {
                $method = $value instanceof Rid\Collection ? 'findRecords' : 'find';
                $value = $this->$method($value->getValue(), null, false);
                $result->getDocument()->$setter($value);
            } elseif (is_array($value)) {
                $value = $this->finalizeCollection($value);
                $result->getDocument()->$setter($value);
            }
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
     * Returns the binding instance used to communicate OrientDB.
     *
     * @return BindingInterface
     */
    protected function getBinding()
    {
        return $this->binding;
    }
}
