<?php

namespace Doctrine\ODM\OrientDB;


use Doctrine\ODM\OrientDB\Collections\ArrayCollection;
use Doctrine\ODM\OrientDB\Mapper\Hydration\Hydrator;
use Doctrine\ODM\OrientDB\Proxy\Proxy;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\OrientDB\Query\Query;

/**
 * Class UnitOfWork
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class UnitOfWork
{
    private $manager;
    private $hydrator;
    private $proxies = array();

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function execute(Query $query, $fetchPlan = null)
    {
        $binding = $this->getManager()->getBinding();
        $results = $binding->execute($query, $fetchPlan)->getResult();

        if (is_array($results) && $query->canHydrate()) {
            $collection = $this->getHydrator()->hydrateCollection($results);

            foreach ($collection as $entity) {
                $this->attach($entity);
            }

            return $collection;
        }

        return true;
    }

    public function hasProxyFor(Rid $rid)
    {
        return isset($this->proxies[$rid->getValue()]);
    }

    /**
     * @param Rid $rid
     * @param bool $lazy
     * @param null $fetchPlan
     *
     * @return Proxy
     */
    public function getProxyFor(Rid $rid, $lazy = true, $fetchPlan = null)
    {
        if (! $this->hasProxyFor($rid)) {
            if ($lazy) {
                $proxy = $this->getHydrator()->hydrateRid($rid);
            } else {
                $proxy = $this->load($rid, $fetchPlan);
            }

            $this->proxies[$rid->getValue()] = $proxy;
        }

        return $this->proxies[$rid->getValue()];
    }

    /**
     * @param string[] $rids
     * @param bool     $lazy
     * @param string   $fetchPlan
     *
     * @return ArrayCollection|null
     */
    public function getCollectionFor(array $rids, $lazy = true, $fetchPlan = null)
    {
        if ($lazy) {
            $proxies = array();
            foreach ($rids as $rid) {
                $proxies[] = $this->getProxyFor(new Rid($rid), $lazy);
            }

            return new ArrayCollection($proxies);
        }

        $results = $this->getHydrator()->load($rids, $fetchPlan);

        if (is_array($results)) {
            return $this->getHydrator()->hydrateCollection($results);
        }

        return null;

    }

    public function attach(Proxy $proxy)
    {
        $this->proxies[$this->getRid($proxy)] = $proxy;
    }

    /**
     * Gets the rid of the proxy.
     *
     * @param Proxy $proxy
     *
     * @return string
     */
    protected function getRid(Proxy $proxy)
    {
        $metadata = $this->getManager()->getClassMetadata(get_parent_class($proxy));

        return $metadata->getIdentifierValues($proxy);
    }

    /**
     * Executes a query against OrientDB to find the specified RID and finalizes the
     * hydration result.
     *
     * Optionally the query can be executed using the specified fetch plan.
     *
     * @param  Rid   $rid
     * @param  mixed $fetchPlan
     * @return object|null
     */
    protected function load(Rid $rid, $fetchPlan = null)
    {
        $results = $this->getHydrator()->load(array($rid->getValue()), $fetchPlan);

        if (isset($results) && count($results)) {
            $record = is_array($results) ? array_shift($results) : $results;
            $results = $this->getHydrator()->hydrate($record);

            return $results;
        }

        return null;
    }


    /**
     * Returns the manager the UnitOfWork is attached to
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     *
     * Lazily instantiates and returns the Hydrator
     *
     * @return Hydrator
     */
    public function getHydrator()
    {
        if (! $this->hydrator) {
            $this->hydrator = new Hydrator($this);
        }

        return $this->hydrator;
    }

    protected function getInflector()
    {
        return $this->getManager()->getInflector();
    }
} 