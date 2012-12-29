<?php

/**
 * Query interface
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query;

interface QueryInterface
{
    /**
     * Adds a relation in a link-list|set.
     *
     * @param   array   $updates
     * @param   string  $class
     * @param   boolean $append
     * @return  Add
     */
    public function add(array $updates, $class, $append);

    /**
     * Alters an attribute of a class.
     *
     * @param   string $class
     * @param   string $attribute
     * @param   string $value
     * @return  Alter
     */
    public function alter($class, $attribute, $value);

    /**
     * Alters the $property of $class setting $sttribute to $value.
     *
     * @param   string $class
     * @param   string $property
     * @param   string $attribute
     * @param   string $value
     * @return  Alter
     */
    public function alterProperty($class, $property, $attribute, $value);

    /**
     * Adds a where condition to the query.
     *
     * @param string  $condition
     * @param mixed   $value
     */
    public function andWhere($condition, $value);

    /**
     * Converts a "normal" select into an index one.
     * You use do a select on an index you can use the between operator.
     *
     * @param   string  $key
     * @param   string  $left
     * @param   string  $right
     */
    public function between($key, $left, $right);

    /**
     * Executes a CREATE of a $class, or of the $property in the given $class if
     * $property is specified.
     *
     * @param   string $class
     * @param   string $property
     * @param   string $type
     * @param   string $linked
     * @return  mixed
     */
    public function create($class, $property);

    /**
     * Executes a DELETE SQL query on the given class (= $from).
     *
     * @param   string $from
     * @return  Delete
     */
    public function delete($from);

    /**
     * Drops a $class, or the $property in the given $class if
     * $property is specified.
     *
     * @param   string $class
     * @param   string $property
     * @return  mixed
     */
    public function drop($class, $property);

    /**
     * Sets the fields to query.
     *
     * @param   array   $fields
     * @param   boolean $append
     * @return  Query
     */
    public function fields(array $fields, $append);

    /**
     * Adds a from clause to the query.
     *
     * @param array   $target
     * @param boolean $append
     */
    public function from(array $target, $append);

    /**
     * Returns the raw SQL query statement.
     *
     * @return String
     */
    public function getRaw();

    /**
     * Returns the tokens associated to the current query.
     *
     * @return array
     */
    public function getTokens();

    /**
     * Converts the query into an GRANT with the given $permission.
     *
     * @param   string  $permission
     * @return  Grant
     */
    public function grant($permission);

    /**
     * Finds documents referencing the document with the given $rid.
     * You can specify to look for only certain $classes, that can be
     * appended.
     *
     * @param   string  $rid
     * @param   array   $classes
     * @param   boolean $append
     * @return  Find
     */
    public function findReferences($rid, array $classes, $append);


    /**
     * Sets the classes in which the query performs is operation.
     * For example a FIND REFERENCES uses the IN in order to find documents
     * referencing to a given document <code>in</code> N classes.
     *
     * @param   array   $in
     * @param   boolean $append
     * @return  mixed
     */
    public function in(array $in, $append);

    /**
     * Creates a index
     *
     * @param   string $property
     * @param   string $class
     * @param   string $type
     * @return  Query
     */
    public function index($property, $class, $type);

    /**
     * Count the elements of the index $indexName.
     *
     * @param string $indexName
     */
    public function indexCount($indexName);

    /**
     * Puts a new entry in the index $indexName with the given $key and $rid.
     *
     * @param string $indexName
     * @param string $key
     * @param string $rid
     */
    public function indexPut($indexName, $key, $rid);

    /**
     * Removes the index $indexName with the given $key/$rid.
     *
     * @param string $indexName
     * @param string $key
     * @param string $rid
     */
    public function indexRemove($indexName, $key, $rid);

    /**
     * Converts the query into an INSERT.
     *
     * @return Query
     */
    public function insert();

    /**
     * Inserts the INTO clause to a query.
     *
     * @param   string $target
     * @return  Query
     */
    public function into($target);

    /**
     * Adds a limit to the current query.
     *
     * @return  $this
     */
    public function limit($limit);

    /**
     * Adds a skip clause to the current query.
     *
     * @return  $this
     */
    public function skip($records);

    /**
     * Sets the internal command to a LINK, which is capable to create a
     * reference from the $property of $class, with a given $alias.
     * You can specify if the link is one-* or two-way with the $inverse
     * parameter.
     *
     * @param   string  $class
     * @param   string  $property
     * @param   string  $alias
     * @param   boolean $inverse
     * @return  Link
     */
    public function link($class, $property, $alias, $inverse);

    /**
     * Sets the ON clause of a query.
     *
     * @param   string $on
     * @return  Query
     */
    public function on($on);

    /**
     * Orders the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     */
    public function orderBy($order, $append, $first);

    /**
     * Adds an OR clause to the query.
     *
     * @param string  $condition
     * @param mixed   $value
     */
    public function orWhere($condition, $value);

    /**
     * Removes a link from a link-set|list.
     *
     * @param   array   $updates
     * @param   string  $class
     * @param   boolean $append
     * @return  Remove
     */
    public function remove(array $updates, $class, $append);

    /**
     * Resets the WHERE conditions.
     *
     * @rerurn  mixed
     */
    public function resetWhere();

    /**
     * Converts the query into an REVOKE with the given $permission.
     *
     * @param   string  $permission
     * @return  Revoke
     */
    public function revoke($permission);

    /**
     * Adds an array of fields into the select part of the query.
     *
     * @param array   $projections
     * @param boolean $append
     */
    public function select(array $projections, $append);

    /**
     * Sets the type clause of a query.
     *
     * @param   string $type
     * @return  Query
     */
    public function type($type);

    /**
     * Adds a subject to the query.
     *
     * @param   string   $to
     * @return  Query
     */
    public function to($to);

    /**
     * Sets the values to be inserted into the current query.
     *
     * @param   array   $values
     * @param   boolean $append
     * @return  Insert
     */
    public function values(array $values, $append);

    /**
     * Removes a index
     *
     * @param   string $property
     * @param   string $class
     * @return  Query
     */
    public function unindex($property, $class);

    public function put(array $values, $class, $append);

    public function update($class);

    /**
     * Adds the WHERE clause.
     *
     * @param string  $condition
     * @param mixed   $value
     */
    public function where($condition, $value = null);
}
