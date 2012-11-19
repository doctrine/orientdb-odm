<?php

/*
 * This file is part of the Doctrine\Orient package.
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
 * @package    Doctrine\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\Orient\ODM;

use Doctrine\Orient\ODM\Mapper;
use Doctrine\Orient\ODM\Mapper\Hydration\Result;
use Doctrine\Orient\Query;
use Doctrine\Orient\Foundation\Types\Rid;
use Doctrine\Orient\Exception\ODM\OClass\NotFound as UnmappedClass;
use Doctrine\Orient\Query\Command\Select;
use Doctrine\Orient\Exception;
use Doctrine\Orient\Exception\Casting\Mismatch;
use Doctrine\Orient\Contract\Binding\BindingInterface;
use Doctrine\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;
use Doctrine\Orient\Validator\Rid as RidValidator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as MetadataFactory;

class Manager implements ObjectManager
{
    protected $mapper;
    protected $binding;
    protected $metadataFactory;

    /**
     * Instatiates a new Mapper, injecting the $mapper that will be used to
     * hydrate record retrieved through the $binding.
     *
     * @param   Mapper           $mapper
     * @param   BindingInterface $binding
     * @param   MetadataFactory  $metadataFactory
     */
    public function __construct(Mapper $mapper, BindingInterface $binding, MetadataFactory $metadataFactory = null)
    {
        $this->mapper = $mapper;
        $this->binding = $binding;
        $this->metadataFactory = $metadataFactory ?: new ClassMetadataFactory($mapper);
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
    public function execute(Query $query)
    {
        $binding = $this->getBinding();
        $results = $binding->execute($query->getRaw(), $query->shouldReturn())->getResult();

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
     * @throws  UnmappedClass|Mismatch|Exception
     */
    public function find($rid, $fetchPlan = null)
    {
        $validator = new RidValidator;
        $rid = $validator->check($rid);

        if ($fetchPlan === false) {
            return new Proxy($this, $rid);
        }

        try {
            return $this->doFind($rid, $fetchPlan);
        } catch (UnmappedClass $e) {
            throw $e;
        } catch (Mismatch $e) {
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
     * @throws  Doctrine\Orient\Exception\Query\SQL\Invalid
     */
    public function findRecords(Array $rids, $fetchPlan = null)
    {
        if ($fetchPlan === false) {
            return new Proxy\Collection($this, $rids);
        }

        $query = new Query($rids);
        $binding = $this->getBinding();
        $results = $binding->execute($query->getRaw(), $fetchPlan)->getResult();

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
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     * @todo implement and test
     */
    function initializeObject($obj)
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
        $query = new Query(array($rid));
        $binding = $this->getBinding();
        $results = $binding->execute($query->getRaw(), $fetchPlan)->getResult();

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
     * Returns the binding instance used to communicate OrientDB.
     *
     * @return BindingInterface
     */
    protected function getBinding()
    {
        return $this->binding;
    }
}
