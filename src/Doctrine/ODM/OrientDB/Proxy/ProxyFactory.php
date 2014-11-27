<?php

namespace Doctrine\ODM\OrientDB\Proxy;


use Doctrine\Common\Persistence\Mapping\ClassMetadata as BaseClassMetadata;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\Common\Proxy\ProxyDefinition;
use Doctrine\Common\Proxy\ProxyGenerator;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;

/**
 * Class ProxyFactory
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class ProxyFactory extends AbstractProxyFactory
{
    /**
     * @var \Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory
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
     * @param \Doctrine\ODM\OrientDB\Manager        $documentManager The DocumentManager the new factory works for.
     * @param string                                $proxyDir        The directory to use for the proxy classes. It
     *                                                               must exist.
     * @param string                                $proxyNamespace  The namespace to use for the proxy classes.
     * @param integer                               $autoGenerate    Whether to automatically generate proxy classes.
     */
    public function __construct(ClassMetadataFactory $metadataFactory, $proxyDir, $proxyNamespace, $autoGenerate = AbstractProxyFactory::AUTOGENERATE_NEVER)
    {
        $this->metadataFactory = $metadataFactory;
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
        $classMetadata     = $this->metadataFactory->getMetadataFor($className);
        return new ProxyDefinition(
            ClassUtils::generateProxyClassName($className, $this->proxyNamespace),
            $classMetadata->getIdentifierFieldNames(),
            $classMetadata->getReflectionProperties(),
            function() { throw new \Exception('To be implemented'); },
            function() { throw new \Exception('To be implemented'); }
        );
    }

} 