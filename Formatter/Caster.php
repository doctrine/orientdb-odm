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

class Caster
{
    public static function castBoolean($value)
    {
        return (bool) $value;
    }
    
    public static function castDate($value)
    {
        return new \DateTime($value);
    }

    public static function castDateTime($value)
    {
        return self::castDate($value);
    }

    public static function castString($value)
    {
        
        if($value instanceOf \StdClass){
            if (!method_exists($value, '__toString')){
                $value = '';
            }
        }
        
        return (string) $value;
    }
}
