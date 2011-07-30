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
     * @param  mixed $value
     * @return boolean
     */    
    public static function castBoolean($value);
    
    /**
     * Casts the given $value to a DateTime object.
     *
     * @param  mixed $value
     * @return boolean
     */
    public static function castDate($value);

    /**
     * Casts the given $value to a DateTime object.
     *
     * @param  mixed $value
     * @return boolean
     */    
    public static function castDateTime($value);

    /**
     * Casts the given $value to string.
     *
     * @param  mixed $value
     * @return boolean
     */    
    public static function castString($value);
}

