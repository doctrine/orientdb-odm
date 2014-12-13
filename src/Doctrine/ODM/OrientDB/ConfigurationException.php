<?php

namespace Doctrine\ODM\OrientDB;


class ConfigurationException extends \Exception
{
    public static function missingKey($key)
    {
        return new self(sprintf('%s must be set in the configuration.', $key));
    }

    public static function invalidPersisterStrategy($strategy, array $accepted)
    {
        return new self(sprintf('You have specified an invalid persister strategy (%s), accepted strategies are %s.', $strategy, implode(', ', $accepted)));
    }
}