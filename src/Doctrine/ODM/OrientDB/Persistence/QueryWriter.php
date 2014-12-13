<?php

namespace Doctrine\ODM\OrientDB\Persistence;


use Doctrine\ODM\OrientDB\Mapper\ClassMetadata;
use Doctrine\ODM\OrientDB\Mapper\ClassMetadataFactory;

/**
 * Class QueryWriter
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class QueryWriter
{
    private $queries = array();

    public function getQueries()
    {
        return $this->queries;
    }

    public function addInsertQuery($identifier, $class, array $fields, $cluster = null)
    {
        $query = "let %s = INSERT INTO %s%s SET %s";
        $cluster = $cluster ? ' cluster '.$cluster : '';
        $this->queries[] = sprintf($query, $identifier, $class, $cluster, $this->flattenFields($fields));

        return count($this->queries)-1;
    }

    public function matchResultToQueries()
    {

    }

    protected function flattenFields(array $fields)
    {
        $string = '';
        foreach ($fields as $name => $value) {
            $string .= sprintf('%s=%s,', $name, $this->escape($value));
        }

        return rtrim($string, ',');
    }

    protected function escape($value)
    {
        return is_string($value) ? "'$value'" : $value;
    }
}