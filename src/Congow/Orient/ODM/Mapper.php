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
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\ODM\Mapper\LinkTracker;
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
use Congow\Orient\ODM\Mapper\Annotations\Reader;
use Doctrine\Common\Util\Inflector as DoctrineInflector;
use Doctrine\Common\Annotations\AnnotationReader;

class Mapper
{
    protected $documentDirectories              = array();
    protected $annotationReader                 = null;
    protected $inflector                        = null;
    protected $enableOverflows                  = false;
    protected $documentProxiesDirectory         = null;
    
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
     * @todo outdated phpdoc
     * @todo use the cached annotation reader
     */
    public function __construct($documentProxyDirectory, AnnotationReaderInterface $annotationReader = null, Inflector $inflector = null)
    {
        $this->annotationReader             = $annotationReader ?: new Reader;
        $this->inflector                    = $inflector ?: new DoctrineInflector;
        $this->documentProxyDirectory       = $documentProxyDirectory;
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
     * Returns the internal object used to parse annotations.
     *
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }
    
    /**
     * Returns the annotation of a class.
     *
     * @param   string                    $class
     * @return  Congow\Orient\ODM\Mapper\Class
     */
    public function getClassAnnotation($class)
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
     * Returns the annotation of a property.
     *
     * @param ReflectionProperty            $property
     * @return Congow\Orient\ODM\Mapper\Property
     */
    public function getPropertyAnnotation(\ReflectionProperty $property)
    {
        return $this->annotationReader->getPropertyAnnotation(
                $property, self::ANNOTATION_PROPERTY_CLASS
        );
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
     * @todo the returning array is ugly, provide a Hydration\Result object
     */
    public function hydrate(\StdClass $orientObject)
    {
        $classProperty = self::ORIENT_PROPERTY_CLASS;

        if (property_exists($orientObject, $classProperty))
        {
            $orientClass  = $orientObject->$classProperty;
            
            if ($orientClass) {
                $class          = $this->findClassMappingInDirectories($orientClass);
                $linkTracker    = new LinkTracker();
                $document       = $this->createDocument($class, $orientObject, $linkTracker);

                return array($document, $linkTracker);
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
     * @todo phpdoc outdated
     */
    protected function createDocument($class, \stdClass $orientObject, LinkTracker $linkTracker)
    {
        $proxyClass = $this->getProxyClass($class);
        $document   = new $proxyClass();
        $this->fill($document, $orientObject, $linkTracker);
        
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
    protected function castProperty($annotation, $propertyValue)
    {
        $caster = new Caster($this);
        $method     = 'cast' . $this->inflector->camelize($annotation->type);
        $caster->setValue($propertyValue);
        $caster->setProperty('annotation', $annotation);
        
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
    protected function fill($document, \stdClass $object, LinkTracker $linkTracker)
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
                        $annotation,
                        $linkTracker
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
     * @todo phpdoc
     */
    protected function generateProxyClass($class, $proxyClassName, $dir)
    {
        $refClass = new \ReflectionClass($class);
        $methods = "";
        $namespace = substr($class, 0, strlen($class) - strlen($proxyClassName) - 1);

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
            
namespace Congow\Orient\Proxy$namespace;
    
class $proxyClassName extends $class
{
  $methods    
}
EOT;
        
        $f = file_put_contents($dir . '/' . $proxyClassName . ".php", $proxy);
    }
    
    /**
     * @todo phpdoc
     */
    protected function getCaster()
    {
        return $this->caster;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getDocumentProxyDirectory()
    {
        return $this->documentProxyDirectory;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getProxyClass($class)
    { 
        $namespaces         = explode('\\', $class);
        $proxyClassName     = array_pop($namespaces);

        if (!class_exists("Congow\Orient\Proxy\\" . $proxyClassName)) {
            $dir = $this->getDocumentProxyDirectory() . '/Congow/Orient/Proxy';
            
            foreach ($namespaces as $namespace) {
                $dir = $dir . '/' . $namespace;
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
            }
            
            $namespace = implode('\\', $namespaces);

            $this->generateProxyClass($class, $proxyClassName, $dir);
        }

        return "Congow\Orient\Proxy" . $namespace . "\\" . $proxyClassName;
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
     * Given a $property and its $value, sets that property on the $given object
     * using a public setter.
     *
     * @param mixed                 $document
     * @param string                $property
     * @param string                $value
     * @param PropertyAnnotation    $annotation
     */
    protected function mapProperty($document, $property, $value, PropertyAnnotation $annotation, LinkTracker $linkTracker)
    {
        if ($annotation->type) {
            $value = $this->castProperty($annotation, $value);

            if($value instanceOf Rid) {
                $value = $value->getValue();
                $linkTracker->add($property, $value);
            }
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