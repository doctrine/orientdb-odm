<?php

namespace Doctrine\ODM\OrientDB;


use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ODM\OrientDB\Mapper\Annotations\Reader;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;
use Doctrine\OrientDB\Util\Inflector\Cached as Inflector;

/**
 * Class Configuration
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class Configuration
{
    private $options;
    private $metadataFactory;
    private $inflector;
    private $cache;
    private $annotationReader;

    public function __construct(array $options)
    {
        $defaults = array('proxyNamespace' => 'Doctrine\ODM\OrientDB\Proxy', 'documentDirectories' => array());

        $this->options = array_merge($defaults ,$options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getProxyDirectory()
    {
        if (! isset($this->options['proxyDirectory'])) {
            throw ConfigurationException::missingKey('proxyDirectory');
        }

        return $this->options['proxyDirectory'];
    }

    public function getProxyNamespace()
    {
        return $this->options['proxyNamespace'];
    }

    public function getAutoGenerateProxyClasses()
    {
        return isset($this->options['autoGenerateProxyClasses']) ? $this->options['autoGenerateProxyClasses'] : null;
    }

    public function getMetadataFactory()
    {
        if (! $this->metadataFactory) {
            $this->metadataFactory = isset($this->options['metadataFactory']) ?
                $this->options['metadataFactory'] : new ClassMetadataFactory($this->getAnnotationReader(), $this->getCache());

            $this->metadataFactory->setDocumentDirectories($this->options['documentDirectories']);
        }

        return $this->metadataFactory;
    }

    public function getInflector()
    {
        if (! $this->inflector) {
            $this->inflector = isset($this->options['inflector']) ?
                $this->options['inflector'] : new Inflector();
        }

        return $this->inflector;
    }

    public function getCache()
    {
        if (! $this->cache) {
            $this->cache = isset($this->options['cache']) ?
                $this->options['cache'] : new ArrayCache();
        }

        return $this->cache;
    }

    public function getAnnotationReader()
    {
        if (! $this->annotationReader) {
            $this->annotationReader = isset($this->options['annotationReader']) ?
                $this->options['annotationReader'] : new Reader();
        }

        return $this->annotationReader;
    }
}
