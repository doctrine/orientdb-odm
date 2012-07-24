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
use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Mapper\Hydration;
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
use Congow\Orient\Exception\Casting\Mismatch;
use Congow\Orient\ODM\Mapper\Annotations\Reader;
use Doctrine\Common\Util\Inflector as DoctrineInflector;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Finder\Finder;

class Mapper
{
    protected $documentDirectories          = array();
    protected $enableMismatchesTolerance    = false;
    protected $annotationReader;
    protected $inflector;
    protected $documentProxiesDirectory;

    const ANNOTATION_PROPERTY_CLASS = 'Congow\Orient\ODM\Mapper\Annotations\Property';
    const ANNOTATION_CLASS_CLASS    = 'Congow\Orient\ODM\Mapper\Annotations\Document';
    const ORIENT_PROPERTY_CLASS     = '@class';

    /**
     * Instantiates a new Mapper, which stores proxies in $documentProxyDirectory
     *
     * @param string                    $documentProxyDirectory
     * @param AnnotationReaderInterface $annotationReader
     * @param Inflector                 $inflector
     */
    public function __construct(
        $documentProxyDirectory,
        AnnotationReaderInterface
        $annotationReader = null,
        Inflector $inflector = null
    ) {
        $this->annotationReader       = $annotationReader ?: new Reader;
        $this->inflector              = $inflector ?: new DoctrineInflector;
        $this->documentProxyDirectory = $documentProxyDirectory;
    }

    /**
     * Enable or disable overflows' tolerance.
     *
     * @see   toleratesMismatches()
     * @param boolean $value
     */
    public function enableMismatchesTolerance($value = true)
    {
        $this->enableMismatchesTolerance = (bool) $value;
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
     * @param   string   $class
     * @return  Congow\Orient\ODM\Mapper\Class
     */
    public function getClassAnnotation($class)
    {
        $reader              = $this->getAnnotationReader();
        $reflClass           = new \ReflectionClass($class);
        $mappedDocumentClass = self::ANNOTATION_CLASS_CLASS;

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
     * @param ReflectionProperty $property
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
     * @return  Hydration\Result
     * @throws  Congow\Orient\Exception\Document\NotFound
     */
    public function hydrate(\StdClass $orientObject)
    {
        $classProperty = self::ORIENT_PROPERTY_CLASS;

        if (property_exists($orientObject, $classProperty)) {
            $orientClass  = $orientObject->$classProperty;

            if ($orientClass) {
                $class       = $this->findClassMappingInDirectories($orientClass);
                $linkTracker = new LinkTracker();
                $document    = $this->createDocument($class, $orientObject, $linkTracker);

                return new Hydration\Result($document, $linkTracker);
            }
        }

        throw new DocumentNotFoundException();
    }

    /**
     * Hydrates an array of documents.
     *
     * @param   Array $json
     * @return  Array
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
     * Creates a new Proxy $class object, filling it with the properties of
     * $orientObject.
     * The proxy class extends from $class and is used to implement
     * lazy-loading.
     *
     * @param   string      $class
     * @param   \stdClass   $orientObject
     * @param   LinkTracker $linkTracker
     * @return  object of type $class
     */
    protected function createDocument(
        $class,
        \stdClass $orientObject,
        LinkTracker $linkTracker
    ) {
        $proxyClass = $this->getProxyClass($class);
        $document   = new $proxyClass();
        $this->fill($document, $orientObject, $linkTracker);

        return $document;
    }

    /**
     * Casts a value according to how it was annotated.
     *
     * @param   Congow\Orient\ODM\Mapper\Annotations\Property  $annotation
     * @param   mixed                                          $propertyValue
     * @return  mixed
     */
    protected function castProperty($annotation, $propertyValue)
    {
        $caster = new Caster($this);
        $method = 'cast' . $this->inflector->camelize($annotation->type);
        $caster->setValue($propertyValue);
        $caster->setProperty('annotation', $annotation);
        $this->verifyCastingSupport($caster, $method, $annotation->type);

        return $caster->$method();
    }

    /**
     * Given an object and an Orient-object, it fills the former with the
     * latter.
     *
     * @param   object      $document
     * @param   \stdClass   $object
     * @param   LinkTracker $linkTracker
     * @return  object
     */
    protected function fill($document, \stdClass $object, LinkTracker $linkTracker)
    {
        $propertyAnnotations = $this->getObjectPropertyAnnotations($document);

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
     * Tries to find the PHP class mapping Congow\OrientDB's $OClass in each of the
     * directories where the documents are stored.
     *
     * @param   string $OClass
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
     * Searches a PHP class mapping Congow\OrientDB's $OClass in $directory,
     * which uses the given $namespace.
     *
     * @param   string                      $OClass
     * @param   string                      $directory
     * @param   string                      $namespace
     * @param   StringFormatterInterface    $stringFormatter
     * @param   \Iterator                   $iterator
     * @return  string|null
     */
    protected function findClassMappingInDirectory(
        $OClass,
        $directory,
        $namespace,
        StringFormatterInterface $stringFormatter = null,
        \Iterator $iterator = null
    ) {
        $stringFormatter = $stringFormatter ?: new StringFormatter;
        $finder          = new Finder;
        $iterator        = $finder->files()->name('*.php')->in($directory);

        foreach ($iterator as $file) {
            $class = $stringFormatter::convertPathToClassName($file, $namespace);

            if (class_exists($class)) {
                $annotation = $this->getClassAnnotation($class);

                if ($annotation && $annotation->hasMatchingClass($OClass)){
                    return $class;
                }
            }
        }

        return null;
    }

    /**
     * Generate a proxy class for the given $class, writing it in the
     * filesystem.
     * A proxy class is a simple class extending $class, copying all its public
     * methods with some rules in order to implement lazy-loading
     *
     * @param type $class
     * @param type $proxyClassName
     * @param type $dir
     * @see   http://congoworient.readthedocs.org/en/latest/implementation-of-lazy-loading.html
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
     * Returns the directory in which all the documents' proxy classes are
     * stored.
     *
     * @return string
     */
    protected function getDocumentProxyDirectory()
    {
        return $this->documentProxyDirectory;
    }

    /**
     * Retrieves the proxy class for the given $class.
     * If the proxy does not exists, it will be generated here at run-time.
     *
     * @param  string $class
     * @return string
     */
    protected function getProxyClass($class)
    {
        $namespaces     = explode('\\', $class);
        $proxyClassName = array_pop($namespaces);

        if (!class_exists("Congow\Orient\Proxy" . $class)) {
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

        return "Congow\Orient\Proxy" . $class;
    }

    /**
     * Returns all the annotations in the $document's properties.
     *
     * @param   mixed $document
     * @return  array
     */
    protected function getObjectPropertyAnnotations($document)
    {
        $refObject   = new \ReflectionObject($document);
        $annotations = array();

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
     * @param mixed                 $document
     * @param string                $property
     * @param string                $value
     * @param PropertyAnnotation    $annotation
     * @param LinkTracker           $linkTracker
     */
    protected function mapProperty($document, $property, $value, PropertyAnnotation $annotation, LinkTracker $linkTracker)
    {
        if ($annotation->type) {
            $value = $this->castProperty($annotation, $value);

            if ($value instanceOf Rid) {
                $linkTracker->add($property, $value);
            }
        }

        $setter     = 'set' . $this->inflector->camelize($property);

        if (method_exists($document, $setter)) {
            $document->$setter($value);
        }
        else {
            $refClass    = new \ReflectionObject($document);
            $refProperty = $refClass->getProperty($property);

            if ($refProperty->isPublic()) {
                $document->$property = $value;
            } else {
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
     * @param   Caster $caster
     * @param   string $method
     * @param   string $annotationType
     * @throws  Congow\Orient\Exception
     */
    protected function verifyCastingSupport(Caster $caster, $method, $annotationType)
    {
        if (!method_exists($caster, $method)) {
            $message  = sprintf(
                'You are trying to map a property wich seems not to have a standard type (%s). Do you have a typo in your annotation? If you think everything\'s ok, go check on %s class which property types are supported.',
                $annotationType,
                get_class($caster)
            );

            throw new Exception($message);
        }
    }
}
