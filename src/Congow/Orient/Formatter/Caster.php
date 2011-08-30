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

class Caster implements CasterInterface
{
    protected $value    = NULL;
    
    const SHORT_LIMIT   = 32767;
    
    public function __construct($value = null)
    {
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
     * Casts the given $value into an integer.
     *
     * @return integer
     */
    public function castInteger()
    {
        return (int) $this->value;
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
        if (abs($this->value) > self::SHORT_LIMIT) {
            $message = sprintf('Short out of bounds (%d of %d)', $this->value, self::SHORT_LIMIT);
            
            throw new Overflow($message);
        }
        
        return $this->value;
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
}
