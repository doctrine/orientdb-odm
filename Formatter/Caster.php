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
 * Caster class
 *
 * @package    Orient
 * @subpackage Formatter
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Formatter;

use Orient\Contract\Formatter\Caster as CasterInterface;

class Caster implements CasterInterface
{
    /**
     * Casts the given $value to boolean.
     *
     * @param  mixed $value
     * @return boolean
     */
    public static function castBoolean($value)
    {
        return (bool) $value;
    }
    
    /**
     * Casts the given $value to a DateTime object.
     *
     * @param  mixed $value
     * @return boolean
     */
    public static function castDate($value)
    {
        return new \DateTime($value);
    }

    /**
     * Casts the given $value to a DateTime object.
     *
     * @param  mixed $value
     * @return boolean
     */
    public static function castDateTime($value)
    {
        return self::castDate($value);
    }

    /**
     * Casts the given $value to string.
     *
     * @param  mixed $value
     * @return boolean
     */    
    public static function castString($value)
    {
        
        if($value instanceOf \StdClass){
            if (!method_exists($value, '__toString')){
                $value = null;
            }
        }
        
        return (string) $value;
    }
}
