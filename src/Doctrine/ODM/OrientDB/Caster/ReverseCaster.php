<?php

namespace Doctrine\ODM\OrientDB\Caster;


use Doctrine\ODM\OrientDB\Persistence\DocumentPersister;

class ReverseCaster extends AbstractCaster
{
    protected $inflector;

    public function __construct(DocumentPersister $persister)
    {
        $this->persister = $persister;
    }


    /**
     * Casts the value to string.
     *
     * @return string
     */
    public function castString()
    {
        if (is_string($this->value)) {
            return $this->value;
        }

        $this->raiseMismatch('string');
    }

    /**
     * Casts the value to a short.
     *
     * @return mixed
     */
    public function castShort()
    {
        return $this->castNumeric();
    }

    /**
     * Casts the value to a long.
     *
     * @return mixed
     */
    public function castLong()
    {
        return $this->castNumeric();
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
        if ("data:;base64," === substr($this->value, 0, 13)) {
            return substr($this->value, 13);
        }

        $this->raiseMisMatch('binary');
    }

    /**
     * Casts the given $value to a byte.
     *
     * @throws CastingMismatchException
     * @return mixed
     */
    public function castByte()
    {
        return $this->castNumeric();
    }

    /**
     * Casts the given $value to a DateTime object.
     * If the value was stored as a timestamp, it sets the value to the DateTime
     * object via the setTimestamp method.
     *
     * @return \DateTime
     */
    public function castDate()
    {
        $dateClass = $this->getDateClass();
        if ($this->value instanceof $dateClass) {
            return $this->value->getTimestamp() * 1000; //ODB takes milliseconds accuracy
        }

        $this->raiseMismatch('\DateTime');
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
     * Casts the internal value to an integer cmprehended between a range of
     * accepted integers.
     *
     * @return integer
     */
    public function castDecimal()
    {
        return $this->castNumeric();
    }

    /**
     * Casts the given $value to a double (well... float).
     *
     * @return float
     */
    public function castDouble()
    {
        return $this->castFloat();
    }

    /**
     * Given an embedded record, it uses the manager to hydrate it.
     *
     * @return mixed
     */
    public function castEmbedded()
    {
        throw new \Exception('to be implemented');
    }

    /**
     * Casts a list of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedList()
    {
        throw new \Exception('to be implemented');
    }

    /**
     * Casts a map (key-value preserved) of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedMap()
    {
        throw new \Exception('to be implemented');

    }

    /**
     * Casts a set of embedded entities
     *
     * @return Array
     */
    public function castEmbeddedSet()
    {
        throw new \Exception('to be implemented');

    }

    public function castLink()
    {
        throw new \Exception('to be implemented');
    }

    public function castLinkSet()
    {
        throw new \Exception('to be implemented');
    }

    public function castLinkList()
    {
        throw new \Exception('to be implemented');
    }

    public function castLinkMap()
    {
        throw new \Exception('to be implemented');
    }

    /**
     * Casts the value to a float.
     *
     * @return float
     */
    public function castFloat()
    {
        return $this->castNumeric();
    }

    /**
     * Casts the value into an integer.
     *
     * @return integer
     */
    public function castInteger()
    {
        return $this->castNumeric();
    }

    /**
     * Checks if the value is numeric, otherwise throws exception.
     * ODB automatically transforms numeric values into the type specified,
     * so there is no reason to cast them.
     *
     * @return int|string
     * @throws CastingMismatchException
     */
    protected function castNumeric()
    {
        if (is_numeric($this->value)) {
            return $this->value;
        }

        $this->raiseMismatch('integer');
    }
} 