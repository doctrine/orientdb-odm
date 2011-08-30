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

class Caster implements CasterInterface
{
    protected $value = NULL;
    
    public function __construct($value = null)
    {
        if ($value) {
            $this->setValue($value);
        }
    }
    
    
    /**
     * Casts the given $value to boolean.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function castBoolean()
    {
        return (bool) $this->value;
    }
    
    /**
     * Casts the given $value to a DateTime object.
     *
     * @param  mixed $value
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
     * @param  mixed $value
     * @return boolean
     */
    public function castDateTime()
    {
        return $this->castDate($this->value);
    }

    /**
     * Casts the given $value to a double (well... float).
     *
     * @param  mixed $value
     * @return float
     */
    public function castDouble()
    {
        return floatval($this->value);
    }

    /**
     * Casts the given $value to string.
     *
     * @param  mixed $value
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
