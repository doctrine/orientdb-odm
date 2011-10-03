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
    protected $className;
    
    /**
    * @todo phpdoc
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
     * @param $rid The identifier.
     * @return object The object.
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
        $query = new Query($this->getOrientClasses());
        
        return $this->getManager()->execute($query);
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
    
    protected function getClassName()
    {
        return $this->className;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getManager()
    {
        return $this->manager;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getOrientClasses()
    {
        $classAnnotation = $this->getMapper()->getClassAnnotation($this->getClassName());
        
        return explode(',', $classAnnotation->class);
    }
}
