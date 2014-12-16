<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Factory
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Mapper;

use Doctrine\Common\Cache\Cache;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory as ClassMetadataFactoryInterface;
use Doctrine\ODM\OrientDB\Mapper\Annotations\ReaderInterface as AnnotationReaderInterface;
use Doctrine\ODM\OrientDB\OClassNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * @todo this class needs to be tested, is part of the core of the ODM
 */
class ClassMetadataFactory implements ClassMetadataFactoryInterface
{
    const ANNOTATION_PROPERTY_CLASS = 'Doctrine\ODM\OrientDB\Mapper\Annotations\Property';
    const ANNOTATION_CLASS_CLASS    = 'Doctrine\ODM\OrientDB\Mapper\Annotations\Document';

    protected $annotationReader;
    protected $cache;
    protected $documentDirectories       = array();
    protected $metadata                  = array();
    protected $classMap                  = array();
    public static $singleAssociations    = array('link');
    public static $multipleAssociations  = array('linklist', 'linkset', 'linkmap');

    /**
     * @param AnnotationReaderInterface $annotationReader
     * @param Cache                     $cache
     */
    public function __construct(AnnotationReaderInterface $annotationReader, Cache $cache)
    {
        $this->annotationReader = $annotationReader;
        $this->cache = $cache;
    }

    /**
     * @to implement and test
     */
    public function getAllMetadata()
    {
        return $this->metadata;
    }

    /**
     * @to implement and test
     *
     * @return ClassMetadata
     */
    public function getMetadataFor($className)
    {
        if (!$this->hasMetadataFor($className)) {
            $metadata = new ClassMetadata($className);
            $this->populateMetadata($metadata);
            $this->setMetadataFor($className, $metadata);
        }

        return $this->metadata[$className];
    }

    /**
     * @to implement and test
     */
    public function hasMetadataFor($className)
    {
        return isset($this->metadata[$className]);
    }

    /**
     * Whether the class with the specified name should have its metadata loaded.
     * This is only the case if it is either mapped directly or as a
     * MappedSuperclass.
     *
     * @param string $className
     * @return boolean
     * @todo to implement and test
     */
    public function isTransient($className)
    {
        throw new \Exception();
    }

    /**
     * @to implement and test
     */
    public function setMetadataFor($className, $metadata)
    {
        $this->metadata[$className] = $metadata;
    }

    /**
     * Returns the directories in which the mapper is going to look for
     * classes mapped for the Doctrine\OrientDB ODM.
     *
     * @return array
     */
    public function getDocumentDirectories()
    {
        return $this->documentDirectories;
    }

    /**
     * Sets the directories in which the mapper is going to look for
     * classes mapped for the Doctrine\OrientDB ODM.
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
     * Returns the fully qualified name of a class by its path
     *
     * @param  string $file
     * @param  string $namespace
     * @return string
     */
    public function getClassByPath($file, $namespace)
    {
        $absPath    = realpath($file);
        $namespaces = explode('/', $absPath);
        $start      = false;
        $i          = 0;
        $chunk      = explode('\\', $namespace);
        $namespace  = array_shift($chunk);

        while ($namespaces[$i] != $namespace) {
            unset($namespaces[$i]);

            if (!array_key_exists(++$i, $namespaces)) {
                break;
            }
        }

        $className = str_replace('.php', null, array_pop($namespaces));

        return '\\'. implode('\\', $namespaces) . '\\' . $className;
    }

    /**
     * Returns the annotation of a class.
     *
     * @param  string   $class
     * @return \Doctrine\ODM\OrientDB\Mapper\Annotations\Document
     */
    public function getClassAnnotation($class)
    {
        $reflClass = new \ReflectionClass($class);
        $mappedDocumentClass = static::ANNOTATION_CLASS_CLASS;

        foreach ($this->annotationReader->getClassAnnotations($reflClass) as $annotation) {
            if ($annotation instanceof $mappedDocumentClass) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * Returns the annotation of a property.
     *
     * @param \ReflectionProperty $property
     * @return \Doctrine\ODM\OrientDB\Mapper\Annotations\Property
     */
    public function getPropertyAnnotation(\ReflectionProperty $property)
    {
        return $this->annotationReader->getPropertyAnnotation(
            $property, self::ANNOTATION_PROPERTY_CLASS
        );
    }

    /**
     * Returns all the annotations in the $document's properties.
     *
     * @param  mixed $document
     * @return array
     */
    public function getObjectPropertyAnnotations($document)
    {
        $cacheKey = "object_property_annotations_" . get_class($document);
        if (!$this->cache->contains($cacheKey)) {
            $refObject   = new \ReflectionObject($document);
            $annotations = array();
            foreach ($refObject->getProperties() as $property) {
                $annotation = $this->getPropertyAnnotation($property);
                if ($annotation) {
                    $annotations[$property->getName()] = $annotation;
                }
            }
            $this->cache->save($cacheKey, $annotations);
        }
        return $this->cache->fetch($cacheKey);
    }

    /**
     * Tries to find the PHP class mapping Doctrine\OrientDB's $OClass in each of the
     * directories where the documents are stored.
     *
     * @param  string $OClass
     * @return string
     * @throws \Doctrine\ODM\OrientDB\OClassNotFoundException
     */
    public function findClassMappingInDirectories($OClass)
    {
        foreach ($this->getDocumentDirectories() as $dir => $namespace) {
            if ($class = $this->findClassMappingInDirectory($OClass, $dir, $namespace)) {
                return $class;
            }
        }

        throw new OClassNotFoundException($OClass);
    }

    protected function populateMetadata(ClassMetadata $metadata)
    {
        $associations = array();
        $fields = array();
        $foundIdentifier = false;

        foreach ($metadata->getReflectionClass()->getProperties() as $refProperty) {
            $annotation = $this->getPropertyAnnotation($refProperty);

            if ($annotation) {
                if ('@rid' === $annotation->name) {
                    $foundIdentifier = true;
                    $metadata->setIdentifier($refProperty->getName());
                    $fields[$refProperty->getName()] = $annotation;
                } elseif (in_array($annotation->type, $this->getAssociationTypes())) {
                    $associations[$refProperty->getName()] = $annotation;
                } else {
                    $fields[$refProperty->getName()] = $annotation;
                }
            }
        }

        if (! $foundIdentifier) {
            throw MappingException::missingRid($metadata->getName());
        }
        $metadata->setFields($fields);
        $metadata->setAssociations($associations);

        return $associations;
    }

    /**
     * Returns all the possible association types.
     * e.g. linklist, linkmap, link...
     *
     * @return Array
     */
    protected function getAssociationTypes()
    {
        return array_merge(static::$singleAssociations, static::$multipleAssociations);
    }

    /**
     * Searches a PHP class mapping Doctrine\OrientDB's $OClass in $directory,
     * which uses the given $namespace.
     *
     * @param  string $OClass
     * @param  string $directory
     * @param  string $namespace
     * @return string|null
     */
    protected function findClassMappingInDirectory($OClass, $directory, $namespace)
    {
        $finder = new Finder();

        if (isset($this->classMap[$OClass])) {
            return $this->classMap[$OClass];
        }

        foreach ($finder->files()->name('*.php')->in($directory) as $file) {
            $class = $this->getClassByPath($file, $namespace);

            if (class_exists($class)) {
                $annotation = $this->getClassAnnotation($class);

                if ($annotation && $annotation->hasMatchingClass($OClass)) {
                    $this->classMap[$OClass] = $class;
                    return $class;
                }
            }
        }

        return null;
    }
}
