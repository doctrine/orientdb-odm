<?php

namespace Doctrine\OrientDB\Util\Inflector;

use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Cache\Cache;

class Cached extends Inflector
{
    protected static $cache = array();
    
    /**
     * @inheritdoc
     */
    public static function tableize($word)
    {
        $cacheKey = "tableize_" . $word;
        
        if (!isset(static::$cache[$cacheKey])) {
            static::$cache[$cacheKey] = parent::tableize($word);
        }
        
        return static::$cache[$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public static function classify($word)
    {
        $cacheKey = "classify_" . $word;
        
        if (!isset(static::$cache[$cacheKey])) {
            static::$cache[$cacheKey] = parent::classify($word);
        }
        
        return static::$cache[$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public static function camelize($word)
    {
        $cacheKey = "camelize_" . $word;
        
        if (!isset(static::$cache[$cacheKey])) {
            static::$cache[$cacheKey] = parent::camelize($word);
        }
        
        return static::$cache[$cacheKey];
    }
}