<?php

namespace Doctrine\ODM\OrientDB\Mapper;


use Doctrine\Common\Cache\Cache;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\OrientDB\Binding\BindingInterface;

/**
 * Class ClusterMap
 *
 * Creates and caches a map of classes and clusters in the
 * database, which makes it possible to tell the proxy class
 * of an entity just by it's rid.
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class ClusterMap
{
    const CACHE_KEY = '_orientdb_%s_cluster_map';

    protected $cache;
    protected $binding;
    protected $map;
    protected $databaseName;

    public function __construct(BindingInterface $binding, Cache $cache)
    {
        $this->binding = $binding;
        $this->cache   = $cache;
        $this->databaseName = $binding->getDatabaseName();
    }

    /**
     * Tries to identify the class of an rid by matching it against
     * the clusters in the database
     *
     * @param Rid $rid
     *
     * @throws MappingException
     * @return string
     */
    public function identifyClass(Rid $rid)
    {
        $map = $this->getMap();
        $splitRid = explode(':', ltrim($rid->getValue(), '#'));
        $clusterId = $splitRid[0];

        foreach ($map as $class => $clusters) {
            if (in_array($clusterId, $clusters)) {
                return $class;
            }
        }

        throw MappingException::noClusterForRid($rid);
    }

    /**
     * Loads/generates the map in case it's needed.
     *
     * @return array
     */
    protected function getMap()
    {
        if (! $this->map) {
            $this->load();
        }

        return $this->map;
    }

    /**
     * Creates the mapping of classes to clusters,
     * it is public so it can be called forcibly
     * which will be handy if it's done in some
     * cache-warmup task.
     *
     */
    public function generateMap()
    {
        $map = array();
        $database = $this->binding->getDatabase()->getData();

        foreach ($database->classes as $class) {
            $map[$class->name] = $class->clusters;
        }

        $this->map = $map;
        $this->cache->save($this->getCacheKey(), $map);
    }

    /**
     * Tries to load the map from cache,
     * otherwise generates it.
     */
    protected function load()
    {
        if ($this->cache->contains($this->getCacheKey())) {
            $this->map = $this->cache->fetch($this->getCacheKey());
        } else {
            $this->generateMap();
        }
    }

    protected function getCacheKey()
    {
        return sprintf(static::CACHE_KEY, $this->databaseName);
    }

} 