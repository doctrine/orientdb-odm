<?php

namespace Doctrine\ODM\OrientDB\Hydration;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\OrientDB\Exception;
use Doctrine\ODM\OrientDB\Caster\Caster;
use Doctrine\ODM\OrientDB\Types\Rid;
use Doctrine\ODM\OrientDB\DocumentNotFoundException;
use Doctrine\ODM\OrientDB\Mapper\Annotations\Property as PropertyAnnotation;
use Doctrine\ODM\OrientDB\Mapper\Hydration\Result;
use Doctrine\ODM\OrientDB\Mapper\LinkTracker;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;
use Doctrine\ODM\OrientDB\Proxy\ProxyFactory;
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
    protected $annotationReader;
    protected $inflector;
    protected $cache;
    protected $caster;
    protected $castedProperties          = array();

    /**
     * @param ProxyFactory         $proxyFactory
     * @param ClassMetadataFactory $metadataFactory
     * @param Inflector            $inflector
     */
    public function __construct(ProxyFactory $proxyFactory, ClassMetadataFactory $metadataFactory, Inflector $inflector)
    {
        $this->proxyFactory = $proxyFactory;
        $this->metadataFactory = $metadataFactory;
        $this->inflector = $inflector;
    }


    /**
     * Takes an Doctrine\OrientDB JSON object and finds the class responsible to map that
     * object.
     * If the class is found, a new POPO is instantiated and the properties inside the
     * JSON object are filled accordingly.
     *
     * @param  \stdClass $orientObject
     * @return Result
     * @throws DocumentNotFoundException
     */
    public function hydrate(\stdClass $orientObject)
    {
        $classProperty = static::ORIENT_PROPERTY_CLASS;

        if (property_exists($orientObject, $classProperty)) {
            $orientClass = $orientObject->$classProperty;

            if ($orientClass) {
                $linkTracker = new LinkTracker();
                $class       = $this->getMetadataFactory()->findClassMappingInDirectories($orientClass);
                $document    = $this->createDocument($class, $orientObject, $linkTracker);

                return new Result($document, $linkTracker);
            }

            throw new DocumentNotFoundException(self::ORIENT_PROPERTY_CLASS.' property empty.');
        }

        throw new DocumentNotFoundException(self::ORIENT_PROPERTY_CLASS.' property not found.');
    }

    /**
     * Hydrates an array of documents.
     *
     * @param  Array $json
     * @return Array
     */
    public function hydrateCollection(array $collection)
    {
        $records = array();

        foreach ($collection as $key => $record) {
            $records[$key] = $this->hydrate($record);
        }

        return $records;
    }

    /**
     * @return ProxyFactory
     */
    protected function getProxyFactory()
    {
        return $this->proxyFactory;
    }

    /**
     * @return ClassMetadataFactory
     */
    protected function getMetadataFactory()
    {
        return $this->metadataFactory;
    }


    /**
     * Creates a new Proxy $class object, filling it with the properties of
     * $orientObject.
     * The proxy class extends from $class and is used to implement
     * lazy-loading.
     *
     * @param  string      $class
     * @param  \stdClass   $orientObject
     * @param  LinkTracker $linkTracker
     * @return object of type $class
     */
    protected function createDocument($class, \stdClass $orientObject, LinkTracker $linkTracker)
    {
        $document = $this->getProxyFactory()->getProxy($class, array('@rid' => $orientObject->{'@rid'}));

        $this->fill($document, $orientObject, $linkTracker);

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
     * @param  LinkTracker $linkTracker
     * @return object
     */
    protected function fill($document, \stdClass $object, LinkTracker $linkTracker)
    {
        $propertyAnnotations = $this->getMetadataFactory()->getObjectPropertyAnnotations($document);

        foreach ($propertyAnnotations as $property => $annotation) {
            $documentProperty = $property;

            if ($annotation->name) {
                $property = $annotation->name;
            }

            if (property_exists($object, $property)) {
                $this->mapProperty(
                    $document,
                    $documentProperty,
                    $object->$property,
                    $annotation,
                    $linkTracker
                );
            }
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
     * Given a $property and its $value, sets that property on the $given object
     * using a public setter.
     * The $linkTracker is used to verify if the property has to be retrieved
     * with an extra query, which is a domain the Mapper should not know about,
     * so it is used only to keep track of properties that the mapper simply
     * can't handle (a typical example is a @rid, which requires an extra query
     * to retrieve the linked entity).
     *
     * Generally the LinkTracker is used by a Manager after he call the
     * ->hydrate() method of its mapper, to verify that the object is ready to
     * be used in the userland application.
     *
     * @param mixed $document
     * @param string $property
     * @param string $value
     * @param PropertyAnnotation $annotation
     * @param LinkTracker $linkTracker
     *
     * @throws Exception
     */
    protected function mapProperty($document, $property, $value, PropertyAnnotation $annotation, LinkTracker $linkTracker)
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

            if ($value instanceof Rid || $value instanceof Rid\Collection || is_array($value)) {
                $linkTracker->add($property, $value);
            }
        }

        $setter = 'set' . $this->inflector->camelize($property);

        if (method_exists($document, $setter)) {
            $document->$setter($value);
        }
        else {
            $refClass       = new \ReflectionObject($document);
            $refProperty    = $refClass->getProperty($property);

            if ($refProperty->isPublic()) {
                $document->$property = $value;
            } else {
                throw new Exception(
                    sprintf("%s has not method %s: you have to added the setter in order to correctly let Doctrine\OrientDB hydrate your object ?",
                        get_class($document),
                        $setter)
                );
            }
        }
    }


    /**
     * Checks whether the Mapper throws exceptions or not when encountering an
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