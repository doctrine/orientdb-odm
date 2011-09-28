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
 * This class is responsible to convert JSON objects to POPOs and viceversa, via
 * Doctrine's annotations library.
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\Exception;
use Congow\Orient\Query;
use Congow\Orient\Exception\Document\NotFound as DocumentNotFoundException;
use Congow\Orient\Formatter\CasterInterface as CasterInterface;
use Congow\Orient\Formatter\Caster;
use Congow\Orient\Contract\Formatter\Inflector;
use Congow\Orient\Filesystem\Iterator\Regex as RegexIterator;
use Congow\Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;
use Congow\Orient\Contract\Formatter\String as StringFormatterInterface;
use Congow\Orient\Formatter\String as StringFormatter;
use Congow\Orient\Contract\ODM\Mapper\Annotations\Reader as AnnotationreaderInterface;
use Congow\Orient\Exception\ODM\OClass\NotFound as ClassNotFoundException;
use Congow\Orient\Exception\Overflow;
use Congow\Orient\Contract\Protocol\Adapter;
use Doctrine\Common\Util\Inflector as DoctrineInflector;
use Doctrine\Common\Annotations\AnnotationReader;

class Mapper
{
    protected $documentDirectories  = array();
    protected $annotationReader     = null;
    protected $inflector            = null;
    protected $enableOverflows      = false;
    protected $protocolAdapter      = null;
    
    const ANNOTATION_PROPERTY_CLASS = 'Congow\Orient\ODM\Mapper\Annotations\Property';
    const ANNOTATION_CLASS_CLASS    = 'Congow\Orient\ODM\Mapper\Annotations\Document';
    const ORIENT_PROPERTY_CLASS     = '@class';

    /**
     * Instantiates a new Mapper with a proper protocol adapter to make
     * it talk with OrientDB.
     *
     * @param Adapter                   $protocolAdapter
     * @param AnnotationReaderInterface $annotationReader
     * @param Inflector                 $inflector
     */
    public function __construct(Adapter $protocolAdapter,AnnotationReaderInterface $annotationReader = null, Inflector $inflector = null)
    {
        $this->protocolAdapter  = $protocolAdapter;
        $this->annotationReader = $annotationReader ?: new AnnotationReader;
        $this->inflector        = $inflector ?: new DoctrineInflector;
    }
    
    /**
     * Enable or disable overflows' tolerance.
     *
     * @see   toleratesOverflow()
     * @param boolean $value 
     */
    public function enableOverflows($value = true)
    {
        $this->enableOverflows = (bool) $value;
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
        if ($lazy) {
            return new Proxy($this, $rid);
        }
        
        try
        {
            $query      = new Query(array($rid));
            $adapter    = $this->getProtocolAdapter();
            
            if ($adapter->execute($query->getRaw()) && $adapter->getResult()) {
              return $this->hydrate($adapter->getResult());
            }
            
            return null;
        }
        catch (Exception $e) {
            return null;
        }
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
        if ($lazy) {
            return new Proxy\Collection($this, $rids);
        }
        
        return $this->hydrateCollection($this->getProtocolAdapter()->findRecords($rids));
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
     * classes mapped for the Congow\Orient ODM.
     *
     * @return array
     */
    public function getDocumentDirectories()
    {
        return $this->documentDirectories;
    }

    /**
     * Takes an Congow\Orient JSON object and finds the class responsible to map that
     * object.
     * If it finds it, he istantiates a new POPO, filling it with the properties
     * inside the JSON object.
     *
     * @param   StdClass    $orientObject
     * @return  mixed
     * @throws  Congow\Orient\Exception\Document\NotFound
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
        
        foreach ($collection as $key => $record) {
            $records[$key] = $this->hydrate($record);
        }
        
        return $records; 
    }
    
    /**
     * Sets the directories in which the mapper is going to look for
     * classes mapped for the Congow\Orient ODM.
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
     * @todo proxy generation should not be here
     * @todo the proxy directory should be injected
     */
    protected function createDocument($class, \stdClass $orientObject)
    {
        $proxyClass = $class . "Proxy";
        $namespaces = explode('\\', $proxyClass);
        $proxyClassName = array_pop($namespaces);
        $namespace = implode("\\", $namespaces);

        if (!class_exists("Congow\Orient\Proxy\\" . $proxyClassName)) {
            $refClass = new \ReflectionClass($class);
            $methods = "";

            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                if (!$refMethod->isStatic()) {
                    $parameters = array();

                    foreach ($refMethod->getParameters() as $parameter) {
                        $parameters[] = "$" . $parameter->getName();
                    }

                    $parametersAsString = implode(', ', $parameters);

                    $methods .= <<<EOT
    public function {$refMethod->getName()}($parametersAsString) {
        \$parent = parent::{$refMethod->getName()}($parametersAsString);

        if (!is_null(\$parent)) { 
            if (\$parent instanceOf \Congow\Orient\ODM\Proxy\AbstractProxy) {
                return \$parent();
            }

            return \$parent;
        }
    }

EOT;
                }
            }
            
            $proxy = <<<EOT
<?php
            
namespace Congow\Orient\Proxy;
    
class $proxyClassName extends $class
{
  $methods    
}
EOT;
            $f = file_put_contents(__DIR__ . "/../../../../proxies/Congow/Orient/Proxy/" . $proxyClassName . ".php", $proxy);
        }
        
        $proxyClass = "Congow\Orient\Proxy\\" . $proxyClassName;
        $document   = new $proxyClass();
        $this->fill($document, $orientObject);
        
        return $document;
    }

    /**
     * Casts a value according to an $annotation.
     *
     * @param   Congow\Orient\ODM\Mapper\Annotations\Property    $annotation
     * @param   mixed                                     $propertyValue
     * @param   CasterInterface                           $caster
     * @return  mixed
     */
    protected function castProperty($annotation, $propertyValue, CasterInterface $caster = null)
    {
        $caster     = $caster ?: new Caster($this);
        $method     = 'cast' . $this->inflector->camelize($annotation->type);
        $caster->setValue($propertyValue);
        $caster->setAnnotation($annotation);
        
        try {
            $this->verifyCastingSupport($caster, $method, $annotation->type);
            
            return $caster->$method();
        }
        catch (Overflow $e) {
            if ($this->toleratesOverflows()) {
                return null;
            }
            
            throw $e;
        }
    }

    /**
     * Given an object and an Orient-object, it fills the former with the
     * latter.
     *
     * @param   object $document
     * @param   \stdClass $object
     * @return  object
     */
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
     * Tries to find the PHP class mapping Congow\OrientDB's $OClass in each of the
     * directories where the documents are stored.
     * 
     * @param   string $OClass
     * @param   \Iterator $iterator
     * @return  string
     * @throws  Congow\Orient\Exception\ODM\OClass\NotFound
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
     * Seraches a PHP class mapping Congow\OrientDB's $OClass in $directory, which uses
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
     * @return  Congow\Orient\ODM\Mapper\Class
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
     * @return Congow\Orient\ODM\Mapper\Property
     */
    protected function getPropertyAnnotation(\ReflectionProperty $property)
    {
        return $this->annotationReader->getPropertyAnnotation(
                $property, self::ANNOTATION_PROPERTY_CLASS
        );
    }
    
    /**
     * Returns the protocol adapter used to communicate with a OrientDB
     * binding.
     *
     * @return Adapter
     */
    protected function getProtocolAdapter()
    {
        return $this->protocolAdapter;
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
        
        if (method_exists($document, $setter)) {
            $document->$setter($value);            
        } 
        else {
            $refClass     = new \ReflectionObject($document);
            $refProperty  = $refClass->getProperty($property);
            
            if ($refProperty->isPublic()) {
                $document->$property = $value;
            } 
            else {
                $message = "%s has not method %s: you have to added the setter in order to correctly let Congow\Orient hydrate your object ?";
                
                throw new Exception(
                        sprintf($message,
                        get_class($document),
                        $setter)
                );
            }
        }
    }
    
    
    /**
     * Checks whether the Mapper throws exceptions or not when encountering an
     * overflow error during hydration.
     *
     * @return bool
     */
    protected function toleratesOverflows()
    {
        return (bool) !$this->enableOverflows;
    }
    
    /**
     * Verifies if the given $caster supports casting with $method.
     * If not, an excepttion is raised.
     *
     * @param Caster $caster
     * @param string $method
     * @param string $annotationType 
     * @throws Congow\Orient\Exception
     */
    protected function verifyCastingSupport(Caster $caster, $method, $annotationType)
    {
        if (!method_exists($caster, $method)) {
            $message  = sprintf(
                'You are trying to map a property wich seems not to have a standard type (%s). Do you have a typo in your annotation? If you think everything\'s ok, go check on %s class which property types are supported.',
                $type,
                get_class($caster)
            );
            
            throw new Exception($message);
        }
    }
}