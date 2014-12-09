<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Caster interface.
 *
 * @package     Doctrine\ODM
 * @subpackage  OrientDB
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Caster;


interface CasterInterface
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
     * Doctrine\OrientDB\ODM\Mapper object, finding it by rid.
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
     * @throws \Doctrine\OrientDB\Overflow
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
     * Defines properties that can be internally used by the caster.
     *
     * @param string    $key
     * @param mixed     $property
     */
    public function setProperty($key, $property);

    /**
     * Sets the internal value to work with.
     *
     * @param mixed $value
     */
    public function setValue($value);
}
