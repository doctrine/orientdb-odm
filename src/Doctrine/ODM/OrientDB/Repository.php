<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Repository class
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\ODM\OrientDB;

use Doctrine\ODM\OrientDB\Collections\ArrayCollection;
use Doctrine\OrientDB\Query\Query;
use Doctrine\OrientDB\Exception;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Util\Inflector;
use RuntimeException;

class Repository implements ObjectRepository
{
    protected $manager;
    protected $className;

    /**
     * Instantiates a new repository.
     *
     * @param string $className type
     * @param Manager $manager
     */
    public function __construct($className, Manager $manager)
    {
        $this->className = $className;
        $this->manager = $manager;
    }

    /**
     * Convenient method that intercepts the find*By*() calls.
     *
     * @param string $method
     * @param array $arguments
     * @return method
     * @throws RuntimeException
     */
    public function __call($method, $arguments)
    {
        if (strpos($method, 'findOneBy') === 0) {
            $property = substr($method, 9);
            $method   = 'findOneBy';
        } elseif (strpos($method, 'findBy') === 0) {
            $property = (substr($method, 6));
            $method   = 'findBy';
        } else {
            throw new RuntimeException(sprintf("The %s repository class does not have a method %s", get_called_class(), $method));
        }

        $property = Inflector::tableize($property);

        foreach ($arguments as $position => $argument) {
            if (is_object($argument)) {
                if (!method_exists($argument, 'getRid')) {
                    throw new RuntimeException("When calling \$repository->find*By*(), you can only pass, as arguments, objects that have the getRid() method (shortly, entitites)");
                }
                $arguments[$position] = $argument->getRid();
            }
        }

        return $this->$method(array($property => $arguments[0]));
    }

    /**
     * Finds an object by its primary key / identifier.
     *
     * @param string $rid The identifier.
     * @return object The object.
     */
    public function find($rid, $fetchPlan = '*:0')
    {
        $document = $this->getManager()->find($rid, $fetchPlan);

        if (!$document) {
            return null;
        }

        if ($this->contains($document)) {
            return $document;
        }

        throw new Exception(
            "You are asking to find record $rid through the repository {$this->getClassName()} " .
            "but the document belongs to another repository (" . get_class($document) . ")"
        );
    }

    /**
     * Finds all objects in the repository.
     *
     * @return mixed The objects.
     */
    public function findAll()
    {
        return $this->findBy(array());
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
    public function findBy(array $criteria, array $orderBy = array(), $limit = null, $offset = null, $fetchPlan = '*:0')
    {
        $results = array();

        foreach ($this->getOrientClasses() as $mappedClass) {
            $query = new Query(array($mappedClass));

            foreach ($criteria as $key => $value) {
                $query->andWhere("$key = ?", $value);
            }

            foreach ($orderBy as $key => $order) {
                $query->orderBy("$key $order");
            }

            if ($limit) {
                $query->limit($limit);
            }

            $collection = $this->getManager()->execute($query, $fetchPlan);

            if (!$collection instanceof ArrayCollection) {
                throw new Exception(
                    "Problems executing the query \"{$query->getRaw()}\". " .
                    "The server returned $collection instead of ArrayCollection."
                );
            }

            $results = array_merge($results, $collection->toArray());
        }

        return new ArrayCollection($results);
    }

    /**
     * Finds a single object by a set of criteria.
     *
     * @param array $criteria
     * @return object The object.
     */
    public function findOneBy(array $criteria)
    {
        $documents = $this->findBy($criteria, array(), 1);

        if ($documents instanceof ArrayCollection && count($documents)) {
            return $documents->first();
        }

        return null;
    }

    /**
     * Returns the POPO class associated with this repository.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Verifies if the $document should belog to this repository.
     *
     * @param  object  $document
     * @return boolean
     */
    protected function contains($document)
    {
        return in_array($this->getClassName(), class_parents(get_class($document)));
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
     * Returns the OrientDB classes which are mapper by the
     * Repository's $className.
     *
     * @return Array
     */
    protected function getOrientClasses()
    {
        $classAnnotation = $this->getManager()->getMetadataFactory()->getClassAnnotation($this->getClassName());

        return explode(',', $classAnnotation->class);
    }
}
