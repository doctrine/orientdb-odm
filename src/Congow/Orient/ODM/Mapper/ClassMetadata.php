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
 * Class ClassMetadata
 *
 * @package     Orient
 * @subpackage  ClassMetadata
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM\Mapper;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineMetadata;

class ClassMetadata implements DoctrineMetadata
{
    protected $class;
    protected $refClass;
    
    /**
     * @todo phpdoc
     * @todo test
     */
    public function __construct($className)
    {
        $this->class = $className;
    }
    
    /**
     * Get fully-qualified class name of this persistent class.
     * 
     * @return string
     */
    function getName()
    {
        return $this->class;
    }
    
    /**
     * Gets the mapped identifier field name.
     * 
     * The returned structure is an array of the identifier field names.
     *
     * @return array
     */
    function getIdentifier()
    {
        return array('@rid');
    }

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return ReflectionClass
     */
    function getReflectionClass()
    {
        if (!$this->refClass) {
            $this->refClass = new \ReflectionClass($this->getName());
        }
        
        return $this->refClass;
    }

    /**
     * Checks if the given field name is a mapped identifier for this class.
     *
     * @param string $fieldName
     * @return boolean
     */
    function isIdentifier($fieldName)
    {
        return ($fieldName == "@rid");
    }

    /**
     * Checks if the given field is a mapped property for this class.
     *
     * @param string $fieldName 
     * @return boolean
     * @todo to implement and test
     */
    function hasField($fieldName)
    {
        
    }

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    function hasAssociation($fieldName)
    {
        
    }

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    function isSingleValuedAssociation($fieldName)
    {
        
    }

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    function isCollectionValuedAssociation($fieldName)
    {
        
    }
    
    /**
     * A numerically indexed list of field names of this persistent class.
     * 
     * This array includes identifier fields if present on this class.
     * 
     * @return array
     * @todo to implement and test
     */
    function getFieldNames()
    {
        
    }
    
    /**
     * A numerically indexed list of association names of this persistent class.
     * 
     * This array includes identifier associations if present on this class.
     * 
     * @return array
     * @todo to implement and test
     */
    function getAssociationNames()
    {
        
    }
    
    /**
     * Returns a type name of this field.
     * 
     * This type names can be implementation specific but should at least include the php types:
     * integer, string, boolean, float/double, datetime.
     * 
     * @param string $fieldName
     * @return string
     * @todo to implement and test
     */
    function getTypeOfField($fieldName)
    {
        
    }
    
    /**
     * Returns the target class name of the given association.
     * 
     * @param string $assocName
     * @return string
     */
    function getAssociationTargetClass($assocName)
    {
        return null;
    }
}

