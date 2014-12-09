<?php

namespace Doctrine\ODM\OrientDB\Proxy;


use Doctrine\Common\Persistence\Mapping\ClassMetadata as BaseClassMetadata;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\Common\Proxy\ProxyDefinition;
use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\OrientDB\DocumentNotFoundException;
use Doctrine\ODM\OrientDB\Mapper\Hydration\Hydrator;
use Doctrine\ODM\OrientDB\Manager;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;
use Doctrine\Common\Proxy\Proxy as BaseProxy;

/**
 * Class ProxyFactory
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class ProxyFactory extends AbstractProxyFactory
{

    /** @var Hydrator  */
    private $hydrator;

    /**
     * @var ClassMetadataFactory
     */
    private $metadataFactory;

    /**
     * @var \Doctrine\ODM\OrientDB\UnitOfWork The UnitOfWork this factory is bound to.
     */
    private $uow;

    /**
     * @var string The namespace that contains all proxy classes.
     */
    private $proxyNamespace;

    /**
     * Initializes a new instance of the <tt>ProxyFactory</tt> class that is
     * connected to the given <tt>DocumentManager</tt>.
     *
     * @param \Doctrine\ODM\OrientDB\Manager $documentManager The DocumentManager the new factory works for.
     * @param string $proxyDir The directory to use for the proxy classes. It
     *                                                               must exist.
     * @param string $proxyNamespace The namespace to use for the proxy classes.
     * @param integer $autoGenerate Whether to automatically generate proxy classes.
     */
    public function __construct(Manager $manager, $proxyDir, $proxyNamespace, $autoGenerate = AbstractProxyFactory::AUTOGENERATE_NEVER)
    {
        $this->metadataFactory = $manager->getMetadataFactory();
        $this->uow        = $manager->getUnitOfWork();
        $this->proxyNamespace  = $proxyNamespace;
        $proxyGenerator        = new ProxyGenerator($proxyDir, $proxyNamespace);
        $proxyGenerator->setPlaceholder('baseProxyInterface', 'Doctrine\ODM\OrientDB\Proxy\Proxy');
        parent::__construct($proxyGenerator, $this->metadataFactory, $autoGenerate);
    }

    public function skipClass(BaseClassMetadata $classMetadata)
    {
        return false;
    }

    public function createProxyDefinition($className)
    {
        $classMetadata = $this->metadataFactory->getMetadataFor($className);
        $reflectionFields = $classMetadata->getReflectionFields();
        $reflectionId = $reflectionFields[$classMetadata->getRidPropertyName()];

        return new ProxyDefinition(
            ClassUtils::generateProxyClassName($className, $this->proxyNamespace),
            $classMetadata->getIdentifierFieldNames(),
            $classMetadata->getReflectionFields(),
            $this->createInitializer($classMetadata, $this->uow->getHydrator(), $reflectionId),
            $this->createCloner($classMetadata, $this->uow->getHydrator(), $reflectionId)
        );
    }

    /**
     * Generates a closure capable of initializing a proxy
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
     * @param \ReflectionProperty $reflectionId
     *
     * @return \Closure
     *
     * @throws \Doctrine\ODM\OrientDB\DocumentNotFoundException
     */
    private function createInitializer(
        BaseClassMetadata $classMetadata,
        Hydrator $hydrator,
        \ReflectionProperty $reflectionId
    )
    {
        if ($classMetadata->getReflectionClass()->hasMethod('__wakeup')) {
            return function (BaseProxy $proxy) use ($reflectionId, $hydrator) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);
                if ($proxy->__isInitialized()) {
                    return;
                }
                $properties = $proxy->__getLazyProperties();
                foreach ($properties as $propertyName => $property) {
                    if (!isset($proxy->$propertyName)) {
                        $proxy->$propertyName = $properties[$propertyName];
                    }
                }
                $proxy->__setInitialized(true);
                $proxy->__wakeup();

                $rid = $reflectionId->getValue($proxy);
                $loaded = $hydrator->load(array($rid));
                if (null === $loaded) {
                    throw DocumentNotFoundException::documentNotFound(get_class($proxy), $rid);
                } else {
                    $hydrator->hydrate($loaded[0], $proxy);
                }

            };
        }

        return function (BaseProxy $proxy) use ($reflectionId, $hydrator) {
            $proxy->__setInitializer(null);
            $proxy->__setCloner(null);
            if ($proxy->__isInitialized()) {
                return;
            }
            $properties = $proxy->__getLazyProperties();
            foreach ($properties as $propertyName => $property) {
                if (!isset($proxy->$propertyName)) {
                    $proxy->$propertyName = $properties[$propertyName];
                }
            }
            $proxy->__setInitialized(true);

            $rid = $reflectionId->getValue($proxy);
            $loaded = $hydrator->load(array($rid));
            if (null === $loaded) {
                throw DocumentNotFoundException::documentNotFound(get_class($proxy), $rid);
            } else {
                $hydrator->hydrate($loaded[0], $proxy);
            }
        };
    }

    /**
     * Generates a closure capable of finalizing a cloned proxy
     *
     * @param \Doctrine\Common\Persistence\Mapping\ClassMetadata $classMetadata
     * @param \ReflectionProperty $reflectionId
     *
     * @return \Closure
     *
     * @throws \Doctrine\ODM\OrientDB\DocumentNotFoundException
     */
    private function createCloner(
        BaseClassMetadata $classMetadata,
        Hydrator $hydrator,
        \ReflectionProperty $reflectionId
    ) {
        return function (BaseProxy $proxy) use ($reflectionId, $hydrator, $classMetadata) {
            if ($proxy->__isInitialized()) {
                return;
            }
            $proxy->__setInitialized(true);
            $proxy->__setInitializer(null);
            $rid    = $reflectionId->getValue($proxy);
            $original = $hydrator->load(array($rid));

            if (null === $original) {
                throw DocumentNotFoundException::documentNotFound(get_class($proxy), $rid);
            }

            $original = $hydrator->hydrate($original[0], $proxy);

            foreach ($classMetadata->getReflectionClass()->getProperties() as $reflectionProperty) {
                $propertyName = $reflectionProperty->getName();
                if ($classMetadata->hasField($propertyName) || $classMetadata->hasAssociation($propertyName)) {
                    $reflectionProperty->setAccessible(true);
                    $reflectionProperty->setValue($proxy, $reflectionProperty->getValue($original));
                }
            }
        };
    }
}