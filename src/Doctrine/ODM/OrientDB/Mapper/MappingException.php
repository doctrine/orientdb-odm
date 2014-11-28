<?php

namespace Doctrine\ODM\OrientDB\Mapper;


class MappingException extends \Exception
{
    public static function missingRid($class)
    {
        return new self(sprintf('The identifier mapping for %s could not be found.', $class));
    }
} 