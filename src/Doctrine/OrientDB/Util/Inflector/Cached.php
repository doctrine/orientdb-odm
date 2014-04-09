<?php

namespace Doctrine\OrientDB\Util\Inflector;

use Doctrine\Common\Util\Inflector;

class Cached extends Inflector
{
    protected static $cache = array();

    /**
     * @inheritdoc
     */
    public static function tableize($word)
    {
        if (!isset(static::$cache['tableize'][$word])) {
            static::$cache['tableize'][$word] = parent::tableize($word);
        }

        return static::$cache['tableize'][$word];
    }

    /**
     * @inheritdoc
     */
    public static function classify($word)
    {
        if (!isset(static::$cache['classify'][$word])) {
            static::$cache['classify'][$word] = parent::classify($word);
        }

        return static::$cache['classify'][$word];
    }

    /**
     * @inheritdoc
     */
    public static function camelize($word)
    {
        if (!isset(static::$cache['camelize'][$word])) {
            static::$cache['camelize'][$word] = parent::camelize($word);
        }

        return static::$cache['camelize'][$word];
    }
}
