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
 * Interface Caster
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Formatter;

interface Caster
{
    /**
     * Casts the given $value to boolean.
     *
     * @return boolean
     */    
    public function castBoolean();
    
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
     * Casts the given $value to string.
     *
     * @return string
     */    
    public function castString();
    
    /**
     * Sets the internal value to cast.
     * 
     * @param   mixed value
     * @return  void
     */
    public function setValue($value);
}

