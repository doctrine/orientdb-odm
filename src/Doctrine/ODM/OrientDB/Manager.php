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

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\OrientDB\Collections\ArrayCollection;
use Doctrine\ODM\OrientDB\Proxy\Proxy;
use Doctrine\ODM\OrientDB\Proxy\ProxyFactory;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\ODM\OrientDB\Caster\CastingMismatchException;
use Doctrine\OrientDB\Exception;
use Doctrine\OrientDB\Binding\BindingInterface;
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as MetadataFactory;

class Manager implements ObjectManager
{
    protected $configuration;
    protected $binding;
    protected $metadataFactory;
    protected $cache;
    protected $proxyFactory;
    protected $uow;

    /**
     * Instatiates a new Mapper, injecting the $mapper that will be used to
     * hydrate record retrieved through the $binding.
     *
     * @param BindingInterface $binding
     * @param Configuration $configuration
     */
    public function __construct(
        BindingInterface $binding,
        Configuration $configuration
    )
    {
        $this->configuration   = $configuration;
        $this->binding         = $binding;
        $this->inflector       = $configuration->getInflector();
        $this->metadataFactory = $configuration->getMetadataFactory();
        $this->cache           = $configuration->getCache();
        $this->uow             = new UnitOfWork($this);
        /**
         * this must be the last since it will require the Manager to be constructed already.
         * TODO fixthis
         */
        $this->proxyFactory    = new ProxyFactory(
            $this,
            $configuration->getProxyDirectory(),
            $configuration->getProxyNamespace(),
            $configuration->getAutoGenerateProxyClasses()
        );
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
     * This method should be used to execute queries which should not return a
     * result (UPDATE, INSERT) or to retrieve multiple objects: to retrieve a
     * single record look at ->find*() methods.
     *
     * @param  Query $query
     *
     * @return array|Mixed
     */
    public function execute(Query $query, $fetchPlan = null)
    {
        return $this->getUnitOfWork()->execute($query, $fetchPlan);
    }

    /**
     * Returns a reference to an entity. It will be lazily and transparently
     * loaded if anything other than the identifier is touched.
     *
     * @param $rid
     *
     * @return Proxy
     */
    public function getReference($rid)
    {
        return $this->getUnitOfWork()->getProxyFor(new Rid($rid), true);
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
     * @param  string $rid
     * @param  string $fetchPlan
     *
     * @return Proxy|object
     * @throws OClassNotFoundException|CastingMismatchException|Exception
     */
    public function find($rid, $fetchPlan = '*:0')
    {
        $validator = new RidValidator;
        $rid       = $validator->check($rid);

        try {
            return $this->getUnitOfWork()->getProxyFor(new Rid($rid), false, $fetchPlan);
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
     * In case of laziness a collection of proxies is
     * returned which contain either uninitialized
     * proxies for entities the UnitOfWork didn't know
     * about yet, or already existing ones.
     *
     * @TODO The fetchPlan is ignored in case of lazy collections
     *
     * @see    ->find()
     *
     * @param  array   $rids
     * @param  boolean $lazy
     * @param  mixed   $fetchPlan
     *
     * @return ArrayCollection
     * @throws \Doctrine\OrientDB\Binding\InvalidQueryException
     */
    public function findRecords(array $rids, $lazy = false, $fetchPlan = '*:0')
    {
        return $this->getUnitOfWork()->getCollectionFor($rids, $lazy, $fetchPlan);
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
     *
     * @return  \Doctrine\Common\Persistence\Mapping\ClassMetadata
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
     * Returns the ProxyFactory associated with this manager.
     *
     * @return ProxyFactory
     */
    public function getProxyFactory()
    {
        return $this->proxyFactory;
    }

    /**
     * Returns the Cache.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns the Inflector associated with this manager.
     *
     * @return Inflector
     */
    public function getInflector()
    {
        return $this->inflector;
    }

    public function getUnitOfWork()
    {
        return $this->uow;
    }

    /**
     * Returns the Repository class associated with the $class.
     *
     * @param  string $className
     * @return Repository
     */
    public function getRepository($className)
    {
        $repositoryClass = $className . "Repository";

        if (class_exists($repositoryClass)) {
            return new $repositoryClass($className, $this);
        }

        return new Repository($className, $this);
    }

    /**
     * Helper method to initialize a lazy loading proxy or persistent collection.
     *
     * This method is a no-op for other objects.
     *
     * @param object $obj
     * @todo  implement and test
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
     * Returns the binding instance used to communicate OrientDB.
     *
     * @return BindingInterface
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * Returns the Configuration of the Manager
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
