<?php

namespace Doctrine\ODM\OrientDB\Mapper;


use Doctrine\ODM\OrientDB\Types\Rid;

/**
 * Class MappingException
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class MappingException extends \Exception
{
    public static function missingRid($class)
    {
        return new self(sprintf('The identifier mapping for %s could not be found.', $class));
    }

    public static function noClusterForRid(Rid $rid)
    {
        return new self(sprintf('There is no cluster for %s.', $rid->getValue()));
    }

    public static function noMappingForProperty($property, $class)
    {
        return new self(sprintf('The %s class has no mapping for %s property.', $class, $property));
    }
}