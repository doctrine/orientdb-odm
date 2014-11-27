<?php

namespace Doctrine\ODM\OrientDB;


class ConfigurationException extends \Exception
{
    public static function missingKey($key)
    {
        return new self(sprintf('%s must be set in the configuration.', $key));
    }
}