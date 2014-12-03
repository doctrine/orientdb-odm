<?php

namespace Doctrine\ODM\OrientDB;


use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Proxy\AbstractProxyFactory;
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

    public function __construct(array $options = array())
    {
        $defaults = array(
            'proxy_namespace' => 'Doctrine\ODM\OrientDB\Proxy',
            'proxy_autogenerate_policy' => AbstractProxyFactory::AUTOGENERATE_FILE_NOT_EXISTS,
            'document_dirs' => array()
        );

        $this->options = array_merge($defaults ,$options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getProxyDirectory()
    {
        if (! isset($this->options['proxy_dir'])) {
            throw ConfigurationException::missingKey('proxy_dir');
        }

        return $this->options['proxy_dir'];
    }

    public function getProxyNamespace()
    {
        return $this->options['proxy_namespace'];
    }

    public function getAutoGenerateProxyClasses()
    {
        return isset($this->options['proxy_autogenerate_policy']) ? $this->options['proxy_autogenerate_policy'] : null;
    }

    public function getMismatchesTolerance()
    {
        return isset($this->options['mismatches_tolerance']) ? $this->options['mismatches_tolerance'] : false;
    }

    public function getMetadataFactory()
    {
        if (! $this->metadataFactory) {
            $this->metadataFactory = isset($this->options['metadata_factory']) ?
                $this->options['metadata_factory'] : new ClassMetadataFactory($this->getAnnotationReader(), $this->getCache());

            $this->metadataFactory->setDocumentDirectories($this->options['document_dirs']);
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
            $this->annotationReader = isset($this->options['annotation_reader']) ?
                $this->options['annotation_reader'] : new Reader();
        }

        return $this->annotationReader;
    }
}
