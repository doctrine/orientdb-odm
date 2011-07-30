<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * (c) David Funaro <ing.davidino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class is responsible to convert JSON objects to POPOs and viceversa, via
 * Doctrine's annotations library.
 *
 * @package    Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Orient\ODM;

use Orient\Exception\Document as Exception;
use Orient\Formatter\CasterInterface as CasterInterface;
use Orient\Formatter\Caster;
use Orient\Contract\Formatter\Inflector;
use Orient\Filesystem\Iterator;
use Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;
use Orient\Contract\Formatter\String as StringFormatterInterface;
use Orient\Formatter\String as StringFormatter;
use Orient\Contract\ODM\Mapper\Annotations\Reader as AnnotationreaderInterface;
use Doctrine\Common\Util\Inflector as DoctrineInflector;
use Doctrine\Common\Annotations\AnnotationReader;

class Mapper
{
    protected $documentDirectories  = array();
    protected $annotationReader     = null;
    protected $inflector            = null;
    
    const ANNOTATION_PROPERTY_CLASS = 'Orient\ODM\Mapper\Annotations\Property';
    const ANNOTATION_CLASS_CLASS    = 'Orient\ODM\Mapper\Annotations\Document';
    const ORIENT_PROPERTY_CLASS     = '@class';

    public function __construct(AnnotationReaderInterface $annotationReader = null, Inflector $inflector = null)
    {
        $this->annotationReader = $annotationReader ?: new AnnotationReader;
        $this->inflector        = $inflector ?: new DoctrineInflector;
    }
    
    /**
     * Returns the internal object used to parse annotations.
     *
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * Returns the directories in which the mapper is going to look for
     * classes mapped for the Orient ODM.
     *
     * @return array
     */
    public function getDocumentDirectories()
    {
        return $this->documentDirectories;
    }

    /**
     * Takes an Orient JSON object and finds the class responsible to map that
     * object.
     * If it finds it, he istantiates a new POPO, filling it with the properties
     * inside the JSON object.
     *
     * @param   StdClass    $orientObject
     * @return  mixed
     * @throws  Orient\Exception\Document\NotFound
     * @todo    3 IFs
     */
    public function hydrate(\StdClass $orientObject)
    {
        $classProperty = self::ORIENT_PROPERTY_CLASS;

        if (property_exists($orientObject, $classProperty))
        {
            $orientClass  = $orientObject->$classProperty;
            
            if ($orientClass) {
                $class = $this->findClassMapping($orientClass);
                
                if($class) {
                    return $this->createDocument($class, $orientObject);
                }
            }
        }

        throw new Exception\NotFound();
    }
    
    /**
     * @param   array $json
     * @return  array of Documents
     */
    public function hydrateCollection(array $collection)
    {
        $records = array();
        
        foreach ($collection as $record) {
            $records[] = $this->hydrate($record);
        }
        
        return $records; 
    }
    
    /**
     * Sets the directories in which the mapper is going to look for
     * classes mapped for the Orient ODM.
     *
     * @param array $directories
     */
    public function setDocumentDirectories(array $directories)
    {
        $this->documentDirectories = array_merge(
                $this->documentDirectories,
                $directories
        );
    }

    /**
     * Creates a new $class object, filling it with the properties of
     * $orientObject.
     *
     * @param string    $class
     * @param \stdClass $orientObject
     * @return class
     */
    protected function createDocument($class, \stdClass $orientObject)
    {
        $document = new $class();
        $this->fill($document, $orientObject);
        
        return $document;
    }

    /**
     * Casts a value according to an $annotation.
     *
     * @param   Orient\ODM\Mapper\Annotations\Property    $annotation
     * @param   mixed                                     $propertyValue
     * @return  mixed
     */
    protected function castProperty($annotation, $propertyValue, CasterInterface $caster = null)
    {
        $caster     = $caster ?: new Caster;
        $inflector  = $this->inflector;
        $method     = 'cast' . $inflector::camelize($annotation->type);
        
        return $caster::$method($propertyValue);
    }

    protected function fill($document, \stdClass $object)
    {
        $propertyAnnotations = $this->getObjectPropertyAnnotations($document);

        foreach ($propertyAnnotations as $property => $annotation)
        {
            $documentProperty = $property;
            
            if ($annotation->name) {
                $property = $annotation->name;
            }

            if (property_exists($object, $property)) {
                $this->mapProperty(
                        $document,
                        $documentProperty,
                        $object->$property,
                        $annotation
                );
            }
        }

        return $document;
    }

    /**
     * Searches a class mapping the corrispective OrientDB $class.
     *
     * @param   string $OClass
     * @return  a class name, null if not found
     * @todo    hardcoded dependency to Iterator
     * @todo    2 FOREACHs, 2 IFs
     */
    protected function findClassMapping($OClass, StringFormatterInterface $stringFormatter = null)
    {
        $stringFormatter = $stringFormatter ?: new StringFormatter;
      
        foreach ($this->getDocumentDirectories() as $dir => $namespace) {
            $regexIterator  = Iterator::getRegexIterator($dir, '/^.*\.php$/i');

            foreach ($regexIterator as $file) {
                $class = $stringFormatter::convertPathToClassName($file, $namespace);

                if (class_exists($class)) {
                    $annotation = $this->getClassAnnotation($class);
                    
                    if($annotation && $annotation->hasMatchingClass($OClass)){
                        return $class;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Returns the annotation of a class.
     *
     * @param   string                    $class
     * @return  Orient\ODM\Mapper\Class
     */
    protected function getClassAnnotation($class)
    {
        $reader        = $this->getAnnotationReader();
        $reflClass     = new \ReflectionClass($class);
        $annotations   = $reader->getClassAnnotations($reflClass);

        if ($annotations && isset($annotations[self::ANNOTATION_CLASS_CLASS])) {
            return $annotations[self::ANNOTATION_CLASS_CLASS];
        }

        return null;
    }

    /**
     * Returns all the annotations in the $document's properties.
     *
     * @param   mixed $document
     * @return  array
     */
    protected function getObjectPropertyAnnotations($document)
    {
        $refObject      = new \ReflectionObject($document);
        $annotations    = array();

        foreach ($refObject->getProperties() as $property) {
            $annotation = $this->getPropertyAnnotation($property);

            if ($annotation) {
                $annotations[$property->getName()] = $annotation;
            }
        }

        return $annotations;
    }

    /**
     * Returns the annotation of a property.
     *
     * @param ReflectionProperty            $property
     * @return Orient\ODM\Mapper\Property
     */
    protected function getPropertyAnnotation(\ReflectionProperty $property)
    {
        return $this->annotationReader->getPropertyAnnotation(
                $property, self::ANNOTATION_PROPERTY_CLASS
        );
    }

    /**
     * Given a $property and its $value, sets that property on the $given object
     * using a public setter.
     *
     * @param mixed                 $document
     * @param string                $property
     * @param string                $value
     * @param PropertyAnnotation    $annotation
     * @todo  better error handling: if there's no setter explicative message "you have to add a setter in order to..."
     */
    protected function mapProperty($document, $property, $value, PropertyAnnotation $annotation)
    {
        if ($annotation->type) {
            $value = $this->castProperty($annotation, $value);
        }

        $inflector  = $this->inflector;
        $setter     = 'set' . $inflector::camelize($property);
        $document->$setter($value);
    }
}