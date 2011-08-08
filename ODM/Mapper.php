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

use Orient\Exception;
use Orient\Exception\Document\NotFound as DocumentNotFoundException;
use Orient\Formatter\CasterInterface as CasterInterface;
use Orient\Formatter\Caster;
use Orient\Contract\Formatter\Inflector;
use Orient\Filesystem\Iterator\Regex as RegexIterator;
use Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;
use Orient\Contract\Formatter\String as StringFormatterInterface;
use Orient\Formatter\String as StringFormatter;
use Orient\Contract\ODM\Mapper\Annotations\Reader as AnnotationreaderInterface;
use Orient\Exception\ODM\OClass\NotFound as ClassNotFoundException;
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
     */
    public function hydrate(\StdClass $orientObject)
    {
        $classProperty = self::ORIENT_PROPERTY_CLASS;

        if (property_exists($orientObject, $classProperty))
        {
            $orientClass  = $orientObject->$classProperty;
            
            if ($orientClass) {
                $class = $this->findClassMappingInDirectories($orientClass);

                return $this->createDocument($class, $orientObject);
            }
        }

        throw new DocumentNotFoundException();
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
     * @param   CasterInterface                           $caster
     * @return  mixed
     * @todo    do we need to pass an entire annotation object to only retrieve "type"?
     */
    protected function castProperty($annotation, $propertyValue, CasterInterface $caster = null)
    {
        $caster     = $caster ?: new Caster;
        $method     = 'cast' . $this->inflector->camelize($annotation->type);
        $caster->setValue($propertyValue);
        
        if (!method_exists($caster, $method)) {
            $message  = sprintf(
                'You are trying to map a property wich seems not to have a standard type (%s). Do you have a typo in your annotation? If you think everything\'s ok, go check on %s class which property types are supported.',
                $annotation->type,
                get_class($caster)
            );
            
            throw new Exception($message);
        }
        
        return $caster->$method();
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
     * Tries to find the PHP class mapping OrientDB's $OClass in each of the
     * directories where the documents are stored.
     * 
     * @param   string $OClass
     * @param   \Iterator $iterator
     * @return  string
     * @throws  Orient\Exception\ODM\OClass\NotFound
     */
    protected function findClassMappingInDirectories($OClass)
    {      
        foreach ($this->getDocumentDirectories() as $dir => $namespace) {
            if ($class = $this->findClassMappingInDirectory($OClass, $dir, $namespace)) {
                return $class;
            }
        }

        throw new ClassNotFoundException($OClass);
    }
    
    /**
     * Seraches a PHP class mapping OrientDB's $OClass in $directory, which uses
     * the given $namespace.
     *
     * @param   string $OClass
     * @param   string $directory
     * @param   string $namespace
     * @param   StringFormatterInterface $stringFormatter
     * @return  string|null
     */
    protected function findClassMappingInDirectory(
            $OClass, 
            $directory, 
            $namespace, 
            StringFormatterInterface $stringFormatter = null, 
            \Iterator $iterator = null
    )
    {        
        $stringFormatter    = $stringFormatter ?: new StringFormatter;
        $iterator           = $iterator ?: new RegexIterator($directory, '/^.*\.php$/i');
        
        foreach ($iterator as $file) {
            $class      = $stringFormatter::convertPathToClassName($file, $namespace);
            $annotation = $this->getClassAnnotation($class);

            if($annotation && $annotation->hasMatchingClass($OClass)){
                return $class;
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
        $reader                 = $this->getAnnotationReader();
        $reflClass              = new \ReflectionClass($class);
        $mappedDocumentClass    = self::ANNOTATION_CLASS_CLASS;

        foreach ($reader->getClassAnnotations($reflClass) as $annotation) {
            if ($annotation instanceOf $mappedDocumentClass) {
                return $annotation;
            }
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
     */
    protected function mapProperty($document, $property, $value, PropertyAnnotation $annotation)
    {
        if ($annotation->type) {
            $value = $this->castProperty($annotation, $value);
        }

        $setter     = 'set' . $this->inflector->camelize($property);
        
        if (method_exists($document, $setter))
        {
            $document->$setter($value);            
        }
        else
        {
            $message = "%s has not method %s: you have to add the setter in order to correctly let Orient hydrate your object";
            
            throw new Exception(
                    sprintf($message),
                    get_class($document),
                    $setter
            );
        }
    }
}