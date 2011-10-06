<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Repository class
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\Query;
use Doctrine\Common\Persistence\ObjectRepository;

class Repository implements ObjectRepository
{
    protected $manager;
    protected $mapper;
    protected $className;
    
    /**
     * Instantiates a new repository.
     *
     * @param type $className
     * @param Manager $manager
     * @param Mapper $mapper 
     */
    public function __construct($className, Manager $manager, Mapper $mapper)
    {
        $this->manager   = $manager;
        $this->className = $className;
        $this->mapper    = $mapper;
    }
    
    /**
     * Finds an object by its primary key / identifier.
     *
     * @param   $rid The identifier.
     * @return  object The object.
     */
    public function find($rid)
    {
        return $this->getManager()->find($rid);
    }

    /**
     * Finds all objects in the repository.
     *
     * @return mixed The objects.
     */
    public function findAll()
    {
        $results = array();
        
        foreach ($this->getOrientClasses() as $mapperClass) {
            $query      = new Query($this->getOrientClasses());
            $results    = array_merge($results, $this->getManager()->execute($query));
        }

        return $results;
    }

    /**
     * Finds objects by a set of criteria.
     *
     * Optionally sorting and limiting details can be passed. An implementation may throw
     * an UnexpectedValueException if certain values of the sorting or limiting details are
     * not supported.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria
     * @return object The object.
     */
    public function findOneBy(array $criteria)
    {
        
    }
    
    /**
     * Returns the POPO class associated with this repository.
     *
     * @return string
     */
    protected function getClassName()
    {
        return $this->className;
    }
    
    /**
     * Returns the manager associated with this repository.
     *
     * @return Manager
     */
    protected function getManager()
    {
        return $this->manager;
    }
    
    /**
     * Returns the mapper associated with this repository.
     *
     * @return Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
    
    /**
     * Returns the OrientDB classes which are mapper by the
     * Repository's $className.
     *
     * @return Array 
     */
    protected function getOrientClasses()
    {
        $classAnnotation = $this->getMapper()->getClassAnnotation($this->getClassName());
        
        return explode(',', $classAnnotation->class);
    }
}
