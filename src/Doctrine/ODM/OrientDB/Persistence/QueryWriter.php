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

        // returned so we can map the rid to the document
        return count($this->queries)-1;
    }

    public function addUpdateQuery($identifier, array $fields, $lock = 'DEFAULT')
    {
        $query = "UPDATE %s SET %s LOCK %s";
        $this->queries[] = sprintf($query, $identifier, $this->flattenFields($fields), $lock);
    }

    /**
     * @TODO cover vertex/edge deletion
     */
    public function addDeleteQuery($identifier, $class, $lock = 'DEFAULT')
    {
        $query = "DELETE FROM %s WHERE @rid = %s LOCK %s";
        $this->queries[] = sprintf($query, $class, $identifier, $lock);
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