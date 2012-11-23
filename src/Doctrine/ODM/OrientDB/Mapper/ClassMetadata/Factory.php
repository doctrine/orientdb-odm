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
 * @package    Orient
 * @subpackage Mapper
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Mapper\ClassMetadata;

use Doctrine\ODM\OrientDB\Mapper\ClassMetadata;
use Doctrine\ODM\OrientDB\Mapper;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

/**
 * @todo this class needs to be tested, is part of the core of the ODM
 */
class Factory implements ClassMetadataFactory
{
    protected $mapper;
    protected $metadata = array();

    /**
     * Instantiates a new Metadata factory, injecting a Mapper which is used to
     * istantiate new Metadatas.
     *
     * @param Mapper $mapper
     */
    public function  __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
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
     */
    public function getMetadataFor($className)
    {
        if (!$this->hasMetadataFor($className)) {
            $metadata = new ClassMetadata($className, $this->getMapper());
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
    function isTransient($className)
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
     * Returns the mapper associated with this Factory.
     *
     * @return Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
}
