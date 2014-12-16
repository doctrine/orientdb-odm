<?php

namespace Doctrine\ODM\OrientDB\Mapper\Hydration;

use Doctrine\ODM\OrientDB\Collections\ArrayCollection;
use Doctrine\ODM\OrientDB\Mapper\ClusterMap;
use Doctrine\ODM\OrientDB\Proxy\Proxy;
use Doctrine\ODM\OrientDB\UnitOfWork;
use Doctrine\OrientDB\Exception;
use Doctrine\ODM\OrientDB\Caster\Caster;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\ODM\OrientDB\DocumentNotFoundException;
use Doctrine\ODM\OrientDB\Mapper\Annotations\Property as PropertyAnnotation;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;
use Doctrine\ODM\OrientDB\Proxy\ProxyFactory;
use Doctrine\OrientDB\Query\Query;
use Symfony\Component\Finder\Finder;

/**
 * Class Hydrator
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class Hydrator
{
    const ORIENT_PROPERTY_CLASS     = '@class';

    protected $proxyFactory;
    protected $metadataFactory;
    protected $enableMismatchesTolerance = false;
    protected $inflector;
    protected $binding;
    protected $uow;
    protected $cache;
    protected $caster;
    protected $castedProperties          = array();
    protected $clusterMap;

    /**
     * @param UnitOfWork $uow
     */
    public function __construct(UnitOfWork $uow)
    {
        $manager = $uow->getManager();

        $this->proxyFactory    = $manager->getProxyFactory();
        $this->metadataFactory = $manager->getMetadataFactory();
        $this->inflector       = $manager->getInflector();
        $this->binding         = $manager->getBinding();
        $this->uow             = $uow;
        $this->clusterMap      = new ClusterMap($this->binding, $manager->getCache());
        $this->caster          = new Caster($this, $this->inflector);

        $this->enableMismatchesTolerance($manager->getConfiguration()->getMismatchesTolerance());
    }

    /**
     * @param string[] $rids
     * @param string   $fetchPlan
     *
     * @return mixed
     */
    public function load(array $rids, $fetchPlan = null)
    {
        $query   = new Query($rids);
        $results = $this->binding->execute($query, $fetchPlan)->getResult();

        return $results;
    }

    /**
     * Takes an Doctrine\OrientDB JSON object and finds the class responsible to map that
     * object.
     * If the class is found, a new POPO is instantiated and the properties inside the
     * JSON object are filled accordingly.
     *
     * @param  \stdClass $orientObject
     * @param  Proxy     $proxy
     * @return Result
     * @throws DocumentNotFoundException
     */
    public function hydrate(\stdClass $orientObject, Proxy $proxy = null)
    {
        $classProperty = static::ORIENT_PROPERTY_CLASS;

        if ($proxy) {
            $this->fill($proxy, $orientObject);

            return $proxy;

        } elseif (property_exists($orientObject, $classProperty)) {
            $orientClass = $orientObject->$classProperty;

            if ($orientClass) {
                $class       = $this->getMetadataFactory()->findClassMappingInDirectories($orientClass);
                $document    = $this->createDocument($class, $orientObject);

                return $document;
            }

            throw new DocumentNotFoundException(self::ORIENT_PROPERTY_CLASS.' property empty.');
        }

        throw new DocumentNotFoundException(self::ORIENT_PROPERTY_CLASS.' property not found.');
    }

    /**
     * Hydrates an array of documents.
     *
     * @param  Array $json
     * @return ArrayCollection
     */
    public function hydrateCollection(array $collection)
    {
        $records = array();

        foreach ($collection as $key => $record) {
            if ($record instanceof \stdClass) {
                $records[$key] = $this->hydrate($record);
            } else {
                $records[$key] = $this->hydrateRid(new Rid($record));
            }
        }

        return new ArrayCollection($records);
    }

    public function hydrateRid(Rid $rid)
    {
        $orientClass = $this->clusterMap->identifyClass($rid);
        $class       = $this->getMetadataFactory()->findClassMappingInDirectories($orientClass);
        $metadata    = $this->getMetadataFactory()->getMetadataFor($class);

        return $this->getProxyFactory()->getProxy($class, array($metadata->getRidPropertyName() => $rid->getValue()));
    }

    /**
     * Returns the ProxyFactory to which the hydrator is attached.
     *
     * @return ProxyFactory
     */
    protected  function getProxyFactory()
    {
        return $this->proxyFactory;
    }

    /**
     * Returns the MetadataFactor.
     *
     * @return ClassMetadataFactory
     */
    protected function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    protected function getUnitOfWork()
    {
        return $this->uow;
    }

    /**
     * Either tries to get the proxy
     *
     *
     * @param  string      $class
     * @param  \stdClass   $orientObject
     * @return object of type $class
     */
    protected function createDocument($class, \stdClass $orientObject)
    {
        $metadata = $this->getMetadataFactory()->getMetadataFor($class);

        /**
         * when a record from OrientDB doesn't have a RID
         * it means it's an embedded object, which can not be
         * lazily loaded.
         */
        if (isset($orientObject->{'@rid'})) {
            $rid = new Rid($orientObject->{'@rid'});
            if ($this->getUnitOfWork()->hasProxyFor($rid)) {
                $document = $this->getUnitOfWork()->getProxyFor($rid);
            } else {
                $document = $this->getProxyFactory()->getProxy($class, array($metadata->getRidPropertyName() => $rid->getValue()));
            }
        } else {
            $class = $metadata->getName();
            $document = new $class;
        }

        $this->fill($document, $orientObject);

        return $document;
    }

    /**
     * Casts a value according to how it was annotated.
     *
     * @param  \Doctrine\ODM\OrientDB\Mapper\Annotations\Property  $annotation
     * @param  mixed                                               $propertyValue
     * @return mixed
     */
    protected function castProperty($annotation, $propertyValue)
    {
        $propertyId = $this->getCastedPropertyCacheKey($annotation->type, $propertyValue);

        if (!isset($this->castedProperties[$propertyId])) {
            $method = 'cast' . $this->inflector->camelize($annotation->type);

            $this->getCaster()->setValue($propertyValue);
            $this->getCaster()->setProperty('annotation', $annotation);
            $this->verifyCastingSupport($this->getCaster(), $method, $annotation->type);

            $this->castedProperties[$propertyId] = $this->getCaster()->$method();
        }

        return $this->castedProperties[$propertyId];
    }

    protected function getCastedPropertyCacheKey($type, $value)
    {
        return get_class() . "_casted_property_" . $type . "_" . serialize($value);
    }

    /**
     * Returns the caching layer of the mapper.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * Given an object and an Orient-object, it fills the former with the
     * latter.
     *
     * @param  object      $document
     * @param  \stdClass   $object
     * @return object
     */
    protected function fill($document, \stdClass $object)
    {
        $metadata = $this->getMetadataFactory()->getMetadataFor(get_class($document));
        $propertyAnnotations = $this->getMetadataFactory()->getObjectPropertyAnnotations($document);

        foreach ($propertyAnnotations as $property => $annotation) {
            $documentProperty = $property;

            if ($annotation->name) {
                $property = $annotation->name;
            }

            if (property_exists($object, $property)) {
                $value = $this->hydrateValue($object->$property, $annotation);
                $metadata->setDocumentValue($document, $documentProperty, $value);
            }
        }

        if ($document instanceof Proxy) {
            $document->__setInitialized(true);
        }

        return $document;
    }


    /**
     * Returns the caster instance.
     *
     * @return \Doctrine\ODM\OrientDB\Caster\Caster
     */
    protected function getCaster()
    {
        return $this->caster;
    }

    /**
     * Hydrates the value
     *
     * @param $value
     * @param PropertyAnnotation $annotation
     *
     * @return mixed|null
     * @throws \Exception
     */
    protected function hydrateValue($value, PropertyAnnotation $annotation)
    {
        if ($annotation->type) {
            try {
                $value = $this->castProperty($annotation, $value);
            } catch (\Exception $e) {
                if ($annotation->isNullable()) {
                    $value = null;
                } else {
                    throw $e;
                }
            }
        }
        return $value;
    }

    /**
     * Sets whether the Hydrator should tolerate mismatches during hydration.
     *
     * @param bool $tolerate
     */
    public function enableMismatchesTolerance($tolerate)
    {
        $this->enableMismatchesTolerance = $tolerate;
    }

    /**
     * Checks whether the Hydrator throws exceptions or not when encountering an
     * mismatch error during hydration.
     *
     * @return bool
     */
    public function toleratesMismatches()
    {
        return (bool) $this->enableMismatchesTolerance;
    }

    /**
     * Verifies if the given $caster supports casting with $method.
     * If not, an exception is raised.
     *
     * @param  Caster $caster
     * @param  string $method
     * @param  string $annotationType
     * @throws Exception
     */
    protected function verifyCastingSupport(Caster $caster, $method, $annotationType)
    {
        if (!method_exists($caster, $method)) {
            $message  = sprintf(
                'You are trying to map a property wich seems not to have a standard type (%s). Do you have a typo in your annotation?'.
                'If you think everything\'s ok, go check on %s class which property types are supported.',
                $annotationType,
                get_class($caster)
            );

            throw new Exception($message);
        }
    }

}