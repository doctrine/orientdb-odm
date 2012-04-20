<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Caster class is responsible for converting an input value to another type.
 *
 * @package    Congow\Orient
 * @subpackage Formatter
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Formatter;

use Congow\Orient\Contract\Formatter\Caster as CasterInterface;
use Congow\Orient\Exception;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\Foundation\Types\Rid;
use Congow\Orient\Validator\Rid as RidValidator;
use Congow\Orient\Exception\Validation as ValidationException;
use Congow\Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;
use Congow\Orient\ODM\Proxy;
use Congow\Orient\ODM\Proxy\Collection as CollectionProxy;
use Congow\Orient\ODM\Proxy\Value as ValueProxy;
use Congow\Orient\Exception\Casting\Mismatch;

class Caster implements CasterInterface
{
    protected $value;
    protected $mapper;
    protected $dateClass;
    protected $properties   = array();
    protected $trueValues   = array(1, '1', 'true');        
    protected $falseValues  = array(0, '0', 'false');

    const SHORT_LIMIT       = 32767;
    const LONG_LIMIT        = 9223372036854775807;
    const BYTE_MAX_VALUE    = 127;
    const BYTE_MIN_VALUE    = -128;
    const MISMATCH_MESSAGE  = "trying to cast %s as %s";

    /**
     * Instantiates a new Caster.
     *
     * @param Mapper    $mapper
     * @param mixed     $value
     * @param string    $dateClass  The class used to cast dates and datetimes
     */
    public function __construct(Mapper $mapper, $value = null, $dateClass = "\DateTime")
    {
        $this->mapper       = $mapper;
        $this->assignDateClass($dateClass);

        if ($value) {
            $this->setValue($value);
        }
    }

    /**
     * Casts the given $value to boolean or tries to guess if it's an implicit
     * boolean value, like the string 'true'.
     *
     * @todo duplicated for truevalues and falsevalues
     * @return boolean
     */
    public function castBoolean()
    {            
        if (is_bool($this->value)) {
            return $this->value;
        }
        
        foreach ($this->trueValues as $true) {
            if ($this->value === $true) {
                return true;
            }
        }
        
        foreach ($this->falseValues as $false) {
            if ($this->value === $false) {
                return false;
            }
        }
        
        $castFunction = function($value) {
            return (bool) $value;
        };
        
        return $this->handleMismatch($castFunction, 'boolean');
    }

    /**
     * Casts the given $value to a binary.
     *
     * @return string
     */
    public function castBinary()
    {
        return 'data:;base64,' . $this->value;
    }

    /**
     * Casts the given $value to a byte.
     * @throws Congow\Orient\Exception\Casting\Mismatch
     * @return mixed
     */
    public function castByte()
    {
        $min = self::BYTE_MIN_VALUE;
        $max = self::BYTE_MAX_VALUE;
        
        $castFunction = function($value) use ($min, $max){
            if ($value < 0) {
                return $min;
            }
            
            return $max;
        };

        if (is_numeric($this->value) && $this->value >= $min && $this->value <= $max){
            return $this->value;
        } else {
            return $this->handleMismatch($castFunction, 'byte');
        }
    }

    /**
     * Casts the given $value to a DateTime object.
     *
     * @return \DateTime
     */
    public function castDate()
    {
        $dateClass = $this->getDateClass();
        $value = preg_replace('/(\s\d{2}:\d{2}:\d{2}):(\d{1,6})/', '$1.$2', $this->value);

        return new $dateClass($value);
    }

    /**
     * Casts the given $value to a DateTime object.
     *
     * @return \DateTime
     */
    public function castDateTime()
    {
        return $this->castDate();
    }

    /**
     * Casts the given $value to a double (well... float).
     *
     * @return float
     */
    public function castDouble()
    {
        $castFunction = function($value){
            return floatval($value);
        };

        if (is_numeric($this->value)){
            return $castFunction($this->value);
        } else {
            return $this->handleMismatch($castFunction, 'double');
        }
    }

    /**
     * Given an embedded record, it uses the manager to hydrate it.
     *
     * @return mixed
     */
    public function castEmbedded()
    {
        return $this->getMapper()->hydrate($this->value);
    }

    /**
     * Casts a list of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedList()
    {
         return $this->castEmbeddedArrays();
    }

    /**
     * Casts a map (key-value preserved) of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedMap()
    {
        $this->convertJsonCollectionToArray();

        return $this->castEmbeddedArrays();
    }

    /**
     * Casts a set of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedSet()
    {
         return $this->castEmbeddedArrays();
    }

    /**
     * Casts the value to a float.
     *
     * @return float
     */
    public function castFloat()
    {
        return (float) $this->value;
    }

    /**
     * Casts the value into an integer.
     *
     * @return integer
     */
    public function castInteger()
    {
        $castFunction = function($value){
            return (int) $value;
        };

        if (is_numeric($this->value)){
            return $castFunction($this->value);
        } else {
            return $this->handleMismatch($castFunction, 'integer');
        }
    }

    /**
     * If the link is a rid, it returns back a rid object, cause the Managar,
     * which eventually will get back the document, will know from the Mapper
     * that the Caster was not able to cast the link (via a LinkTracker object),
     * so the manager will do an extra query to retrieve the link.
     * If the internal value is not a rid but an already decoded orient
     * object, it simply hydrates it through the mapper.
     *
     * @see     http://code.google.com/p/orient/wiki/FetchingStrategies
     * @return  ValueProxy|Rid
     */
    public function castLink()
    {
        if ($this->value instanceOf \stdClass) {

            return new ValueProxy($this->getMapper()->hydrate($this->value));
        } else {
            try {
                return new Rid($this->value);
            } catch (ValidationException $e) {
                return null;
            }
        }
    }

    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @return Array
     */
    public function castLinkset()
    {
        return $this->castLinkCollection();
    }

    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @return Array
     */
    public function castLinklist()
    {
        return $this->castLinkCollection();
    }

    /**
     * Hydrates multiple objects through a Mapper.
     * A conversion needs to be done because of the non linearity of a JSON
     * collection compared to a PHP array.
     *
     * @return Array
     */
    public function castLinkmap()
    {
        $this->convertJsonCollectionToArray();

        return $this->castLinkCollection();
    }

    /**
     * Casts the given $value to a long.
     *
     * @return mixed
     */
    public function castLong()
    {
        return $this->castInBuffer(self::LONG_LIMIT, 'long');
    }

    /**
     * Casts the current value into an integer verifying it belongs to a certain
     * range ( -$limit < $value > + $limit ).
     *
     * @param integer   $limit
     * @param string    $type
     * @return integer
     * @throws Congow\Orient\Exception\Casting\Mismatch
     */
    public function castInBuffer($limit, $type)
    {
        $castFunction = function($value){
            return $value;
        };

        if (abs($this->value) < $limit) {
            return $castFunction($this->value);
        } else {
            return $this->handleMismatch($castFunction, $type);
        }

    }

    /**
     * Casts the value to string.
     *
     * @return string
     */
    public function castString()
    {
        $castFunction = function($value){
            return (string) $value;
        };

        if (is_string($this->value)) {
            return $castFunction($this->value);
        } else {
            return $this->handleMismatch($castFunction, 'string');
        }
    }

    /**
     * Casts the value to a short.
     *
     * @return mixed
     */
    public function castShort()
    {
        return $this->castInBuffer(self::SHORT_LIMIT, 'short');
    }

    /**
     * Defines properties that can be internally used by the caster.
     *
     * @param string    $key
     * @param mixed     $property
     */
    public function setProperty($key, $property)
    {
        $this->properties[$key] = $property;
    }

    /**
     * Sets the internal value to work with.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Assigns the class used to cast dates and datetimes.
     * If the $class is a subclass of \DateTime, it uses it, it uses \DateTime
     * otherwise.
     *
     * @param string $class
     */
    protected function assignDateClass($class)
    {
        $refClass = new \ReflectionClass($class);

        if (!($refClass->getName() === 'DateTime' || $refClass->isSubclassOf('DateTime'))) {
            throw new \InvalidArgumentException("The class used to cast DATE and DATETIME values must be derived from DateTime.");
        }

        $this->dateClass = $class;
    }

    /**
     * Given a $type, it casts each element of the value array with a method.
     *
     * @param   string $type
     * @return  Array
     */
    protected function castArrayOf($type)
    {
        $method         = 'cast' . ucfirst($type);
        $results        = array();
        $innerCaster    = new self($this->getMapper());

        if (!method_exists($innerCaster, $method)) {
            throw new Congow\Orient\Exception();
        }

        foreach ($this->value as $key => $value) {
            $innerCaster->setValue($value);
            $results[$key] = $innerCaster->$method();
        }

        return $results;
    }

    /**
     * Casts embedded entities, given the $cast property of the internal
     * annotation.
     *
     * @return Array
     */
    public function castEmbeddedArrays()
    {
        $annotation = $this->getProperty('annotation');

        if (!$annotation) {
            $message =  "In order to cast collections you should inject\n";
            $message .= "an annotation object into the caster.";

            throw new Exception($message);
        }

        $listType = $annotation->getCast();

        if ($listType == "link") {
            return $this->getMapper()->hydrateCollection($this->value);
        }

        try {
            return $this->castArrayOf($listType);
        }
        catch (Congow\Orient\Exception $e) {
            $message  = "Seems like you are trying to hydrate an embedded ";
            $message .= "property without specifying its type.\n";
            $message .= "Please add the 'cast' (eg cast='boolean') ";
            $message .= "to the annotation.";

            throw new Congow\Orient\Exception($message);
        }
    }

    /**
     * Given the internl value of the caster (an array), it iterates iver each
     * element of the array and hydrates it.
     *
     * @see     Caster::castLink for more insights
     * @return  Array|null
     */
    protected function castLinkCollection()
    {
        foreach ($this->value as $key => $value) {
            if (is_object($value)) {
                return $this->getMapper()->hydrateCollection($this->value);
            }

            try {
                $ridCollection = new Rid\Collection(array_map(function($rid){
                    new Rid($rid);

                    return $rid;
                }, $this->value));

                return $ridCollection;
            } catch (ValidationException $e) {
                return null;
            }
        }

        return array();
    }

    /**
     * If a JSON value is converted in an object containing other objects to
     * hydrate, this method converts the main object in an array.
     */
    protected function convertJsonCollectionToArray()
    {
        if(!is_array($this->value) && is_object($this->value)) {
            $orientObjects = array();

            $refClass = new \ReflectionObject($this->value);

            $properties = $refClass->getProperties(\ReflectionProperty::IS_PUBLIC);
            foreach ($properties as $property) {
                $orientObjects[$property->name] = $this->value->{$property->name};
            }

            $this->setValue($orientObjects);
        }
    }


    /**
     * Returns the class used to cast date and datetimes.
     *
     * @return string
     */
    protected function getDateClass()
    {
        return $this->dateClass;
    }

    /**
     * Returns the internl manager.
     *
     * @return Mapper
     */
    protected function getMapper()
    {
        return $this->mapper;
    }

    /**
     * Returns a property of the Caster, given its $key.
     *
     * @param   string $key
     * @return  mixed
     */
    protected function getProperty($key)
    {
        return isset($this->properties[$key]) ? $this->properties[$key] : null;
    }

    /**
     * @todo phpdoc
     */
    protected function handleMismatch(\Closure $castFunction, $expectedType)
    {
        if ($this->mapper->toleratesMismatches()) {
            return $castFunction($this->value);
        }

        $this->raiseMismatch($expectedType);
    }

    /**
     * Throws an exception whenever $value can not be casted as $expectedType.
     */
    protected function raiseMismatch($expectedType)
    {
        throw new Mismatch(sprintf(self::MISMATCH_MESSAGE, $this->value, $expectedType));
    }
}
