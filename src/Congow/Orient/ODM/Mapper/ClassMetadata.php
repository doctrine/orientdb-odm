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

use Congow\Orient\ODM\Mapper as DataMapper;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineMetadata;

class ClassMetadata implements DoctrineMetadata
{
    protected $class;
    protected $refClass;
    protected $mapper;
    private   $singleAssociations   = array('link');
    private   $multipleAssociations = array('linklist', 'linkset', 'linkmap');
    
    /**
     * @todo phpdoc
     * @todo test
     */
    public function  __construct($className, DataMapper $mapper)
    {
        $this->class  = $className;
        $this->mapper = $mapper;
    }
    
    /**
     * Get fully-qualified class name of this persistent class.
     * 
     * @return string
     */
    public function  getName()
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
    public function  getIdentifier()
    {
        return array('@rid');
    }

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return ReflectionClass
     */
    public function  getReflectionClass()
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
    public function  isIdentifier($fieldName)
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
    public function  hasField($fieldName)
    {
        return (bool) $this->getField($fieldName);
    }

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    public function  hasAssociation($fieldName)
    {
        return (bool) $this->getAssociation($fieldName);
    }

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    public function  isSingleValuedAssociation($fieldName)
    {
        return $this->isValuedAssociation($fieldName, $this->singleAssociations);
    }

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     * @todo to implement and test
     */
    public function  isCollectionValuedAssociation($fieldName)
    {
        return $this->isValuedAssociation($fieldName, $this->multipleAssociations);
    }
    
    /**
     * A numerically indexed list of field names of this persistent class.
     * 
     * This array includes identifier fields if present on this class.
     * 
     * @return array
     * @todo to implement and test
     */
    public function  getFieldNames()
    {
        $names = array();

        foreach ($this->getFields() as $field) {
            $names[] = $field->name;
        }
        
        return $names;
    }
    
    /**
     * A numerically indexed list of association names of this persistent class.
     * 
     * This array includes identifier associations if present on this class.
     * 
     * @return array
     * @todo to implement and test
     */
    public function  getAssociationNames()
    {
        $names = array();

        foreach ($this->getAssociations() as $field) {
            $names[] = $field->name;
        }
        
        return $names;
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
    public function  getTypeOfField($fieldName)
    {
        if ($field = $this->getField($fieldName)) {
            return $field->type;
        }
        
        return null;
    }
    
    /**
     * Returns the target class name of the given association.
     * 
     * @param string $assocName
     * @return string
     */
    public function  getAssociationTargetClass($assocName)
    {
        return null;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getAssociation($fieldName)
    {
        foreach ($this->getAssociations() as $association) {
            if ($association->name === $fieldName) {
                return $association;
            }
        }
        
        return null;
    }    
    
    /**
     * @todo phpdoc
     */
    protected function getAssociations()
    {
        $associations = array();
        
        foreach ($this->getReflectionClass()->getProperties() as $refProperty) {
            $association = $this->getMapper()->getPropertyAnnotation($refProperty);

            if ($association && in_array($association->type, $this->getAssociationTypes())) {
                $associations[] = $association;
            }
        }
        
        return $associations;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getAssociationTypes()
    {
        return array_merge($this->singleAssociations, $this->multipleAssociations);
    }
    
    /**
     * @todo phpdoc
     */
    protected function getField($fieldName)
    {        
        foreach ($this->getFields() as $field) {
            if ($field->name === $fieldName) {
                return $field;
            }
        }
        
        return null;
    }
    
    /**
     * @todo phpdoc
     */
    protected function getFields()
    {
        $fields = array();

        foreach ($this->getReflectionClass()->getProperties() as $refProperty) {
            $field = $this->getMapper()->getPropertyAnnotation($refProperty);
         
            if ($field && !in_array($field->type, $this->getAssociationTypes())) {
                 $fields[] = $field;
            }
        }

         return $fields;
    }
    
    /**
     * @todo phpdoc
     */
    protected function isValuedAssociation($fieldName, Array $associationTypes)
    {
        $association = $this->getAssociation($fieldName);
        
        if ($association) {
            return in_array($association->type, $associationTypes);
        }
    }
    
    /**
     * @todo phpdoc
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
}

