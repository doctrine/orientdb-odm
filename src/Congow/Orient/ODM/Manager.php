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
use Doctrine\Common\Persistence\ObjectManager;
use Congow\Orient\ODM\Mapper\ClassMetadata\Factory as ClassMetadataFactory;

class Manager implements ObjectManager
{
    protected $mapper;
    protected $metadataFactory;
    
    /**
     * @param   Mapper $mapper
     * @todo    inject the metadata factory
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper           = $mapper;
        $this->metadataFactory  = new ClassMetadataFactory();
    }
    
    /**
     * delegate the hydration of orientDB record to the mapper
     * @param JSON $json
     * @return mixed the hydrated object
     * @todo test and phpdoc
     */
    public function hydrate($json)
    {
        return $this->mapper->hydrate($json);
    }
    
    /**
     * get the document directories paths
     * @return Array 
     */
    public function getDocumentDirectories()
    {
        return $this->mapper->getDocumentDirectories();
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
     * Enable or disable overflows' tolerance.
     *
     * @see   toleratesOverflow()
     * @param boolean $value 
     */
    public function enableOverflows($value = true)
    {
        $this->mapper->enableOverflows($value);
    }

    /**
     * Via a protocol adapter, it queries for an object with the given $rid.
     * If $lazy loading is used, all of this won't be executed unless the
     * returned Proxy object is called via __invoke, e.g.:
     * 
     * <code>
     *   $lazyLoadedRecord = $mapper->find('1:1', true);
     * 
     *   $record = $lazyLoadedRecord();
     * </code>
     *
     * @param string    $rid
     * @param boolean   $lazy
     * @return Proxy|object
     */
    public function find($rid, $lazy = false){
        return $this->mapper->find($rid, $lazy);
    }
    
    /**
     * Via a protocol adapter, it queries for an array of objects with the given
     * $rids.
     * If $lazy loading is used, all of this won't be executed unless the
     * returned Proxy object is called via __invoke, e.g.:
     * 
     * <code>
     *   $lazyLoadedRecords = $mapper->find('1:1', true);
     * 
     *   $records = $lazyLoadedRecord();
     * </code>
     *
     * @param string    $rid
     * @param boolean   $lazy
     * @return Proxy\Collection|array
     */
    public function findRecords(Array $rids, $lazy = false){
        return $this->mapper->findRecords($rids, $lazy);
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
     * Returns the internal object used to parse annotations.
     *
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->mapper->getAnnotationReader();
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
     * @param   array $json
     * @return  array of Documents
     */
    public function hydrateCollection(array $collection)
    {
        return $this->mapper->hydrateCollection($collection);
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
     * Set the document directories paths
     * @param Array $directories
     * @return void
     */
    public function setDocumentDirectories(array $directories)
    {
        $this->mapper->setDocumentDirectories($directories);
    }    
}