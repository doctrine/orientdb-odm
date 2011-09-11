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
use Congow\Orient\Exception\Overflow;
use Congow\Orient\ODM\Mapper;
use Congow\Orient\Validator\Rid as RidValidator;
use Congow\Orient\Exception\Validation as ValidationException;
use Congow\Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;

/**
 * @todo check @return types, some are wrong
 */
class Caster implements CasterInterface
{
    protected $value    = null;
    protected $mapper   = null;
    
    const SHORT_LIMIT       = 32767;
    const LONG_LIMIT        = 9223372036854775807;
    const BYTE_MAX_VALUE    = 127;
    const BYTE_MIN_VALUE    = -128;
    
    /**
     * Instantiates a new Caster.
     *
     * @param Mapper $mapper
     * @param type $value 
     */
    public function __construct(Mapper $mapper, $value = null)
    {
        $this->mapper = $mapper;
        
        if ($value) {
            $this->setValue($value);
        }
    }
    
    
    /**
     * Casts the given $value to boolean.
     *
     * @return boolean
     */
    public function castBoolean()
    {
        return (bool) $this->value;
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
     *
     * @return boolean
     */
    public function castByte()
    {
        if ($this->value > self::BYTE_MAX_VALUE || $this->value < self::BYTE_MIN_VALUE) {
            $message = sprintf('byte out of bounds (%d of %d)', $this->value, self::SHORT_LIMIT);
            
            throw new Overflow($message);
        }
        
        return $this->value;
    }
    
    /**
     * Casts the given $value to a DateTime object.
     *
     * @return boolean
     * @todo is it possible to decide which class to return and not only datetime?
     */
    public function castDate()
    {
        return new \DateTime($this->value);
    }

    /**
     * Casts the given $value to a DateTime object.
     *
     * @return boolean
     */
    public function castDateTime()
    {
        return $this->castDate($this->value);
    }

    /**
     * Casts the given $value to a double (well... float).
     *
     * @return float
     */
    public function castDouble()
    {
        return floatval($this->value);
    }
    
    /**
     * @todo phpdoc 
     */
    public function castEmbedded()
    {
        return $this->getMapper()->hydrate($this->value);
    }
    
    /**
     * @todo phpdoc 
     * @todo annotations should use getters, not public properties
     * @todo throw custom exception, add an explicative essage ("please add the cast to embeddedlists..")
     * @todo probably theres a better way instead of a crappy switch
     * @todo missing Date, DateTime... 
     */
    public function castEmbeddedList()
    {
        $listType = $this->getAnnotation()->cast;
        
        switch ($listType) {
            case "link":
                $value = $this->getMapper()->hydrateCollection($this->value);
                break;
            case "integer":
                $value = $this->castArrayOf('integer');
                break;
            case "string":
                $value = $this->castArrayOf('string');
                break;
            case "boolean":
                $value = $this->castArrayOf('boolean');
                break;
            default:
                $value = null;
        }
        
        if (!$value) {
            throw new \Exception();
        }
        
        return $value;
    }

    /**
     * Casts the given $value to a float.
     *
     * @return float
     */
    public function castFloat()
    {
        return (float) $this->value;
    }

    /**
     * Casts the given $value into an integer.
     *
     * @return integer
     */
    public function castInteger()
    {
        return (int) $this->value;
    }
    
    /**
     * Casts the current internal value into an hydrated object through a
     * Congow\Orient\ODM\Mapper object, finding it by rid.
     * If the internal value is not a rid but an already decoded orient
     * object, it simply hydrates it.
     *
     * @see     http://code.google.com/p/orient/wiki/FetchingStrategies
     * @return  \stdObject|null
     */
    public function castLink()
    {
        $validator = new RidValidator;
        
        if ($this->value instanceOf \stdClass) {
            return $this->getMapper()->hydrate($this->value);
        } else {
            try {
                $rid = $validator->check($this->value);
                
                return $this->getMapper()->find($rid);
            } catch (ValidationException $e) {
                return null;
            }
        }
    }
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @todo   missing lazy loading, like in castLink
     * @return Array
     */
    public function castLinkset()
    {        
        return $this->castLinkCollection();
    }
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @todo   missing lazy loading, like in castLink
     * @return Array
     */
    public function castLinklist()
    {        
        return $this->castLinkCollection();
    }
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @todo   missing lazy loading, like in castLink
     * @return Array
     */
    public function castLinkmap()
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
        
        return $this->castLinkCollection();
    }
    
    /**
     * Casts the given $value to a long.
     *
     * @return integer
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
     * @throws Congow\Orient\Exception\Overflow
     */
    public function castInBuffer($limit, $type)
    {
        if (abs($this->value) > $limit) {
            $message = sprintf($type . ' out of bounds (%d of %d)', $this->value, self::SHORT_LIMIT);
            
            throw new Overflow($message);
        }
        
        return $this->value;
    }

    /**
     * Casts the given $value to string.
     *
     * @return boolean
     */    
    public function castString()
    {
        if($this->value instanceOf \StdClass){
            if (!method_exists($this->value, '__toString')){
                $this->value = null;
            }
        }
        
        return (string) $this->value;
    }

    /**
     * Casts the given $value to a short.
     *
     * @return integer
     */    
    public function castShort()
    {
        return $this->castInBuffer(self::SHORT_LIMIT, 'long');
    }
    
    public function setAnnotation(PropertyAnnotation $annotation)
    {
        $this->annotation = $annotation;
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
     * @todo phpdoc
     */
    protected function castArrayOf($type)
    {
        $method         = 'cast' . ucfirst($type);
        $results        = array();
        $innerCaster    = new self($this->getMapper());
        
        foreach ($this->value as $key => $value) {
            $innerCaster->setValue($value);
            $results[$key] = $innerCaster->$method();
        }
        
        return $results;
    }
    
    /**
     * @todo missing phpdoc
     */
    protected function castLinkCollection()
    {   
        foreach ($this->value as $key => $value) {
            
            if (is_object($value)) {
                return $this->getMapper()->hydrateCollection($this->value);
            }
            
            try {
                $validator      = new RidValidator();
                $rid            = $validator->check($value);
                
                return $this->getMapper()->findRecords($this->value);
            } catch (ValidationException $e) {
                return null;
            }
        }
    }
    
    /**
     *
     * @todo phpdoc
     */
    protected function getAnnotation()
    {
        return $this->annotation;
    }
    
    /**
     *
     * @todo phpdoc
     */
    protected function getMapper()
    {
        return $this->mapper;
    }
}
