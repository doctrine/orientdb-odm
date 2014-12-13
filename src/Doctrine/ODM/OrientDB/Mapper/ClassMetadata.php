<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Doctrine\ODM\OrientDB\Mapper;

use Doctrine\ODM\OrientDB\Mapper as DataMapper;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as DoctrineMetadata;

/**
 * Class ClassMetadata
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class ClassMetadata implements DoctrineMetadata
{
    protected $orientClass;
    protected $class;
    protected $refClass;
    protected $reflFields;

    protected $identifierPropertyName;
    protected $associations;
    protected $fields;

    /**
     * Instantiates a new Metadata for the given $className.
     *
     * @param string        $className
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
    public function getName()
    {
        return $this->class;
    }

    public function setIdentifier($property)
    {
        $this->identifierPropertyName = $property;
    }

    /**
     * Gets the mapped identifier field name.
     *
     * The returned structure is an array of the identifier field names.
     *
     * @return array
     */
    public function getIdentifier()
    {
        return array($this->identifierPropertyName);
    }

    /**
     * PHP 5.3, no array dereferencing..
     *
     * @return string
     */
    public function getRidPropertyName()
    {
        return $this->identifierPropertyName;
    }

    /**
     * Gets the ReflectionClass instance for this mapped class.
     *
     * @return \ReflectionClass
     */
    public function getReflectionClass()
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
    public function isIdentifier($fieldName)
    {
        return ($fieldName === '@rid');
    }

    /**
     * Checks if the given field is a mapped property for this class.
     *
     * @param string $fieldName
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return (bool) $this->getField($fieldName);
    }

    /**
     * Checks if the given field is a mapped association for this class.
     *
     * @param string $fieldName
     * @return boolean
     */
    public function hasAssociation($fieldName)
    {
        return (bool) $this->getAssociation($fieldName);
    }

    /**
     * Checks if the given field is a mapped single valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isSingleValuedAssociation($fieldName)
    {
        return $this->isValuedAssociation($fieldName, ClassMetadataFactory::$singleAssociations);
    }

    /**
     * Checks if the given field is a mapped collection valued association for this class.
     *
     * @param string $fieldName
     * @return boolean
     */
    public function isCollectionValuedAssociation($fieldName)
    {
        return $this->isValuedAssociation($fieldName, ClassMetadataFactory::$multipleAssociations);
    }

    /**
     * A numerically indexed list of field names of this persistent class.
     *
     * This array includes identifier fields if present on this class.
     *
     * @return Array
     */
    public function getFieldNames()
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
     * @return Array
     */
    public function getAssociationNames()
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
     * @param   string $fieldName
     * @return  string
     */
    public function getTypeOfField($fieldName)
    {
        if ($field = $this->getField($fieldName)) {
            return $field->type;
        }

        return null;
    }

    /**
     * Returns the target class name of the given association.
     *
     * @param   string $assocName
     * @return  string
     */
    public function getAssociationTargetClass($assocName)
    {
        return null;
    }

    public function getReflectionProperties()
    {
        return $this->getReflectionClass()->getProperties();
    }

    /**
     * @return \ReflectionProperty[]
     */
    public function getReflectionFields()
    {
        if (! $this->reflFields) {
            $this->discoverReflectionFields();
        }

        return $this->reflFields;
    }

    protected function discoverReflectionFields()
    {
        $this->reflFields = array();
        foreach ($this->getReflectionProperties() as $property) {
            $this->reflFields[$property->getName()] = $property;
        }
    }

    /**
     * @todo to implement/test
     *
     * @return array
     */
    public function getIdentifierFieldNames()
    {
        return array($this->identifierPropertyName);
    }

    /**
     * @todo to implement/test
     *
     * @param string $assocName
     * @return boolean
     */
    public function isAssociationInverseSide($assocName)
    {
        throw new \Exception('to be implemented');
    }

    /**
     * @todo to implement/test
     *
     * @param string $assocName
     * @return string
     */
    public function getAssociationMappedByTargetField($assocName)
    {
        throw new \Exception('to be implemented');
    }

    /**
     * @todo to test
     *
     * @param object $object
     * @return array
     */
    public function getIdentifierValues($object)
    {
        $fields = $this->getReflectionFields();
        return $fields[$this->identifierPropertyName]->getValue($object);
    }

    /**
     * Returns the association mapped for the given $field.
     *
     * @param   string $field
     * @return  string
     */
    protected function getAssociation($field)
    {
        foreach ($this->getAssociations() as $association) {
            if ($association->name === $field) {
                return $association;
            }
        }

        return null;
    }

    /**
     * @param \Doctrine\ODM\OrientDB\Mapper\Annotations\Property[] $associations
     */
    public function setAssociations(array $associations)
    {
        $this->associations = $associations;
    }

    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Returns the ODB field name mapped to the property
     * or null if it is not mapped to a field.
     *
     * @param string $property
     *
     * @return string|null
     */
    public function getFieldNameForProperty($property)
    {
        $mappedField = $this->getField($property);
        if ($mappedField) {
            return $mappedField->name ? :$property;
        }

        return null;
    }

    public function setOrientClass($orientClass)
    {
        $this->orientClass = $orientClass;
    }

    public function getOrientClass()
    {
        return $this->orientClass;
    }

    /**
     * Given a $property and its $value, sets that property on the given $document
     * by using a closures if available, otherwise fall back to reflection.
     *
     * @param mixed $document
     * @param string $property
     * @param string $value
     */
    public function setDocumentValue($document, $property, $value)
    {
        if (method_exists('\Closure', 'bind')) {
            $setter = \Closure::bind(function ($document, $property, $value) {
                    $document->$property = $value;
                }, null, $document
            );
            $setter($document, $property, $value);
        } else {
            $this->getReflectionClass()->getProperty($property)->setValue($document, $value);
        }
    }

    /**
     * Returns all the possible associations mapped in the introspected class.
     *
     * @return Array
     */
    protected function getAssociations()
    {
        return $this->associations;
    }

    /**
     * Returns the annotation associated with the $field.
     *
     * @param   string $field
     * @return  Annotations\Property
     */
    public function getField($field)
    {
        foreach ($this->getFields() as $annotatedField) {
            if ($annotatedField->name === $field) {
                return $annotatedField;
            }
        }

        return null;
    }

    /**
     * Returns all the fields of the introspected class.
     *
     * @return Array
     */
    protected function getFields()
    {
        return $this->fields;
    }

    /**
     * Checks whether the $field is mapped as an association.
     *
     * @param   string  $field
     * @param   array   $associationTypes
     * @return  boolean
     */
    protected function isValuedAssociation($field, Array $associationTypes)
    {
        $association = $this->getAssociation($field);

        if ($association) {
            return in_array($association->type, $associationTypes);
        }
    }

}
