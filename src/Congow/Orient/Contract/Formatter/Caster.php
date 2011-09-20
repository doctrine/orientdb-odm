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
 * Interface Caster
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\Formatter;

use Congow\Orient\ODM\Mapper\Annotations\Property as PropertyAnnotation;

interface Caster
{     
    /**
     * Casts the given $value to boolean.
     *
     * @return boolean
     */
    public function castBoolean();
    
    /**
     * Casts the given $value to a binary.
     *
     * @return string
     */
    public function castBinary();
    
    /**
     * Casts the given $value to a byte.
     *
     * @return mixed
     */
    public function castByte();
    
    /**
     * Casts the given $value to a DateTime object.
     *
     * @return \DateTime
     */
    public function castDate();

    /**
     * Casts the given $value to a DateTime object.
     *
     * @return \DateTime
     */
    public function castDateTime();

    /**
     * Casts the given $value to a double (well... float).
     *
     * @return float
     */
    public function castDouble();
    
    /**
     * Given an embedded record, it uses the mapper to hydrate it.
     *
     * @return mixed
     */
    public function castEmbedded();
    
    /**
     * Casts a list of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedList();
    
    /**
     * Casts a map (key-value preserved) of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedMap();
    
    /**
     * Casts a set of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedSet();

    /**
     * Casts the value to a float.
     *
     * @return float
     */
    public function castFloat();

    /**
     * Casts the value into an integer.
     *
     * @return integer
     */
    public function castInteger();
    
    /**
     * Casts the current internal value into an hydrated object through a
     * Congow\Orient\ODM\Mapper object, finding it by rid.
     * If the internal value is not a rid but an already decoded orient
     * object, it simply hydrates it.
     *
     * @see     http://code.google.com/p/orient/wiki/FetchingStrategies
     * @return  mixed|null
     */
    public function castLink();
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @return Array
     */
    public function castLinkset();
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @return Array
     */
    public function castLinklist();
    
    /**
     * Hydrates multiple objects through a Mapper.
     *
     * @return Array
     */
    public function castLinkmap();
    
    /**
     * Casts the given $value to a long.
     *
     * @return mixed
     */    
    public function castLong();
    
    /**
     * Casts the current value into an integer verifying it belongs to a certain
     * range ( -$limit < $value > + $limit ).
     *
     * @param integer   $limit
     * @param string    $type
     * @return integer
     * @throws Congow\Orient\Exception\Overflow
     */
    public function castInBuffer($limit, $type);

    /**
     * Casts the value to string.
     *
     * @return string
     */    
    public function castString();

    /**
     * Casts the value to a short.
     *
     * @return mixed
     */    
    public function castShort();
    
    /**
     * Defines the internl annotation object which is used when hydrating
     * collections.
     *
     * @param PropertyAnnotation $annotation 
     */
    public function setAnnotation(PropertyAnnotation $annotation);
    
    /**
     * Sets the internal value to work with.
     *
     * @param mixed $value 
     */
    public function setValue($value);
}

