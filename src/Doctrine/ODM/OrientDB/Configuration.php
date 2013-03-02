<?php

namespace Doctrine\ODM\OrientDB;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\OrientDB\Binding\BindingInterface;

class Configuration
{
    /**
     * @var BindingInterface
     */
    protected $binding;

    /**
     * @var string
     */
    protected $classMetadataFactoryName = 'Doctrine\ODM\OrientDB\Mapper\ClassMetadata\Factory';

    /**
     * @var string
     */
    protected $defaultRepositoryClassName = 'Doctrine\ODM\OrientDB\Repository';

    /**
     * @var MappingDriver
     */
    protected $metadataDriver;

    /**
     * Set the metadata driver
     *
     * @param MappingDriver $mappingDriver
     * @return void
     *
     * @todo Force parameter to be a Closure to ensure lazy evaluation
     * (as soon as a metadata cache is in effect, the driver never needs to initialize).
     */
    public function setMetadataDriver(MappingDriver $mappingDriver)
    {
        $this->metadataDriver = $mappingDriver;
    }

    /**
     * Get the metadata driver
     *
     * @return MappingDriver
     */
    public function getMetadataDriver()
    {
        return $this->metadataDriver;
    }

    /**
     * Set binding
     *
     * @param BindingInterface $binding
     */
    public function setBinding(BindingInterface $binding)
    {
        $this->binding = $binding;
    }

    /**
     * Get bindung
     *
     * @return BindingInterface
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * Adds a new default annotation driver with a correctly configured annotation reader. If $useSimpleAnnotationReader
     * is true, the notation `@Entity` will work, otherwise, the notation `@ODM\Entity` will be supported.
     *
     * @param array $paths
     * @param bool $useSimpleAnnotationReader
     *
     * @return AnnotationDriver
     */
    public function newDefaultAnnotationDriver($paths = array(), $useSimpleAnnotationReader = true)
    {
        /** @todo update this */
        AnnotationRegistry::registerFile(__DIR__ . '/Mapping/Driver/DoctrineAnnotations.php');

        if ($useSimpleAnnotationReader) {
            // Register the ORM Annotations in the AnnotationRegistry
            $reader = new SimpleAnnotationReader();
            $reader->addNamespace('Doctrine\ODM\OrientDB\Mapper'); /* @todo rename directory to Mapping */
            $cachedReader = new CachedReader($reader, new ArrayCache());

            return new AnnotationDriver($cachedReader, (array) $paths); /* @todo write class */
        }

        return new AnnotationDriver(
            new CachedReader(new AnnotationReader(), new ArrayCache()),
            (array) $paths
        );
    }

    /**
     * Sets a class metadata factory.
     *
     * @param string $cmfName
     * @return void
     */
    public function setClassMetadataFactoryName($classMetadataFactoryName)
    {
        $this->classMetadataFactoryName = $classMetadataFactoryName;
    }

    /**
     * Get class metadata factory name
     *
     * @return string
     */
    public function getClassMetadataFactoryName()
    {
        return $this->classMetadataFactoryName;
    }

    /**
     * Set default repository class
     *
     * @param string $className
     * @return void
     * @throws ODMException If not is a \Doctrine\Common\Persistence\ObjectRepository
     */
    public function setDefaultRepositoryClassName($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        if ( ! $reflectionClass->implementsInterface('Doctrine\Common\Persistence\ObjectRepository')) {
            throw ODMException::invalidDocumentRepository($className);
        }
        $this->defaultRepositoryClassName = $className;
    }

    /**
     * Get default repository class.
     *
     * @return string
     */
    public function getDefaultRepositoryClassName()
    {
        return $this->defaultRepositoryClassName;
    }
}
