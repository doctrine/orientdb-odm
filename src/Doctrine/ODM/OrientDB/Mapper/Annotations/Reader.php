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
 * Class used in order to decouple Doctrine\OrientDB from the Doctrine dependency.
 * If you want to use a custom annotation reader library you should make your
 * reader extend this class.
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Mapper\Annotations;

use Doctrine\Common\Annotations\AnnotationReader;
use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Annotations\Parser;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Reader implements ReaderInterface
{
    protected $reader;

    /**
     * Instantiates a new annotation reader, optionally injecting a cache
     * mechanism for it.
     * This reader is basically a proxy wrapping Doctrine's one.
     *
     * @param Cache $cacheReader
     */
    public function __construct(Cache $cacheReader = null)
    {
        if (!$cacheReader) {
            $cacheReader = $this->createCacheProvider();
        }

        $this->reader = new CachedReader(new AnnotationReader, $cacheReader);

        AnnotationRegistry::registerAutoloadNamespace("Doctrine\OrientDB");
        AnnotationRegistry::registerFile( __DIR__ . '/Document.php');
        AnnotationRegistry::registerFile( __DIR__ . '/Property.php');
    }

    /**
     * Gets the annotations applied to a class.
     *
     * @param ReflectionClass $class The ReflectionClass of the class from which
     * the class annotations should be read.
     * @return array An array of Annotations.
     */
    public function getClassAnnotations(\ReflectionClass $class)
    {
        return $this->getReader()->getClassAnnotations($class);
    }

    /**
     * Gets a class annotation.
     *
     * @param ReflectionClass $class The ReflectionClass of the class from which
     * the class annotations should be read.
     * @param string $annotation The name of the annotation.
     * @return The Annotation or null, if the requested annotation does not exist.
     */
    public function getClassAnnotation(\ReflectionClass $class, $annotation)
    {
        return $this->getReader()->getClassAnnotation($class, $annotation);
    }

    /**
     * Gets the annotations applied to a property.
     *
     * @param string|ReflectionProperty $property The name or ReflectionProperty of the property
     * from which the annotations should be read.
     * @return array An array of Annotations.
     */
    public function getPropertyAnnotations(\ReflectionProperty $property)
    {
        return $this->getReader()->getPropertyAnnotations($property);
    }

    /**
     * Gets a property annotation.
     *
     * @param ReflectionProperty $property
     * @param string $annotation The name of the annotation.
     * @return The Annotation or null, if the requested annotation does not exist.
     */
    public function getPropertyAnnotation(\ReflectionProperty $property, $annotation)
    {
        return $this->getReader()->getPropertyAnnotation($property, $annotation);
    }

    /**
     * Gets the annotations applied to a method.
     *
     * @param ReflectionMethod $property The name or ReflectionMethod of the method from which
     * the annotations should be read.
     * @return array An array of Annotations.
     */
    public function getMethodAnnotations(\ReflectionMethod $method)
    {
        return $this->getReader()->getMethodAnnotations($method);
    }

    /**
     * Gets a method annotation.
     *
     * @param ReflectionMethod $method
     * @param string $annotation The name of the annotation.
     * @return The Annotation or null, if the requested annotation does not exist.
     */
    public function getMethodAnnotation(\ReflectionMethod $method, $annotation)
    {
        return $this->getReader()->getMethodAnnotation($method, $annotation);
    }

    /**
     * Returns the cached reader associated with this reader.
     *
     * @return CachedReader
     */
    protected function getReader()
    {
        return $this->reader;
    }

    /**
     * Creates a new instance of a cache provider.
     *
     * @return Doctrine\Common\Cache\CacheProvider
     */
    protected function createCacheProvider()
    {
        return extension_loaded('xcache') ? new XcacheCache() : new ArrayCache();
    }
}
