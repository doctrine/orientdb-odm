<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Query class to build queries execute by an Doctrine\OrientDB's protocol adapter.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query;

use Doctrine\OrientDB\Exception;
use Doctrine\OrientDB\Query\Validator\ValidationException;
use Doctrine\OrientDB\Query\Command\Credential\Grant;
use Doctrine\OrientDB\Query\Command\Credential\Revoke;
use Doctrine\OrientDB\Query\Command\Insert;
use Doctrine\OrientDB\Query\Command\Select;
use Doctrine\OrientDB\Query\Validator\Rid as RidValidator;

class Query implements QueryInterface
{
    protected $command = null;
    protected $commands = array(
        'select'            => 'Doctrine\OrientDB\Query\Command\Select',
        'insert'            => 'Doctrine\OrientDB\Query\Command\Insert',
        'delete'            => 'Doctrine\OrientDB\Query\Command\Delete',
        'update'            => 'Doctrine\OrientDB\Query\Command\Update',
        'update.add'        => 'Doctrine\OrientDB\Query\Command\Update\Add',
        'update.remove'     => 'Doctrine\OrientDB\Query\Command\Update\Remove',
        'update.put'        => 'Doctrine\OrientDB\Query\Command\Update\Put',
        'grant'             => 'Doctrine\OrientDB\Query\Command\Credential\Grant',
        'revoke'            => 'Doctrine\OrientDB\Query\Command\Credential\Revoke',
        'class.create'      => 'Doctrine\OrientDB\Query\Command\OClass\Create',
        'class.drop'        => 'Doctrine\OrientDB\Query\Command\OClass\Drop',
        'class.alter'       => 'Doctrine\OrientDB\Query\Command\OClass\Alter',
        'truncate.class'    => 'Doctrine\OrientDB\Query\Command\Truncate\OClass',
        'truncate.cluster'  => 'Doctrine\OrientDB\Query\Command\Truncate\Cluster',
        'truncate.record'   => 'Doctrine\OrientDB\Query\Command\Truncate\Record',
        'references.find'   => 'Doctrine\OrientDB\Query\Command\Reference\Find',
        'property.create'   => 'Doctrine\OrientDB\Query\Command\Property\Create',
        'property.drop'     => 'Doctrine\OrientDB\Query\Command\Property\Drop',
        'property.alter'    => 'Doctrine\OrientDB\Query\Command\Property\Alter',
        'index.drop'        => 'Doctrine\OrientDB\Query\Command\Index\Drop',
        'index.create'      => 'Doctrine\OrientDB\Query\Command\Index\Create',
        'index.count'       => 'Doctrine\OrientDB\Query\Command\Index\Count',
        'index.put'         => 'Doctrine\OrientDB\Query\Command\Index\Put',
        'index.remove'      => 'Doctrine\OrientDB\Query\Command\Index\Remove',
        'index.lookup'      => 'Doctrine\OrientDB\Query\Command\Index\Lookup',
        'index.rebuild'     => 'Doctrine\OrientDB\Query\Command\Index\Rebuild',
        'link'              => 'Doctrine\OrientDB\Query\Command\Create\Link',
    );

    /**
     * Builds a query with the given $command on the given $target.
     *
     * @param array   $target
     * @param string  $command
     */
    public function __construct(array $target = array(), array $commands = array())
    {
        $this->setCommands($commands);

        $commandClass = $this->getCommandClass('select');
        $this->command = new $commandClass($target);
    }

    /**
     * Adds a relation in a link-list|set.
     *
     * @param   array   $updates
     * @param   string  $class
     * @param   boolean $append
     * @return  Add
     */
    public function add(array $updates, $class, $append = true)
    {
        $commandClass = $this->getCommandClass('update.add');
        $this->command = new $commandClass($updates, $class, $append);

        return $this->command;
    }

    /**
     * Alters an attribute of a class.
     *
     * @param   string $class
     * @param   string $attribute
     * @param   string $value
     * @return  Alter
     */
    public function alter($class, $attribute, $value)
    {
        $commandClass = $this->getCommandClass('class.alter');
        $this->command = new $commandClass($class, $attribute, $value);

        return $this->command;
    }

    /**
     * Alters the $property of $class setting $sttribute to $value.
     *
     * @param   string $class
     * @param   string $property
     * @param   string $attribute
     * @param   string $value
     * @return  Alter
     */
    public function alterProperty($class, $property, $attribute, $value)
    {
        $commandClass = $this->getCommandClass('property.alter');
        $this->command = new $commandClass($property);

        return $this->command->on($class)->changing($attribute, $value);
    }

    /**
     * Adds a where condition to the query.
     *
     * @param string  $condition
     * @param mixed   $value
     */
    public function andWhere($condition, $value = null)
    {
        return $this->command->andwhere($condition, $value);
    }

    /**
     * Converts a "normal" select into an index one.
     * You use do a select on an index you can use the between operator.
     *
     * @param   string  $key
     * @param   string  $left
     * @param   string  $right
     */
    public function between($key, $left, $right)
    {
        return $this->command->between($key, $left, $right);
    }

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
    public function create($class, $property = null, $type = null, $linked = null)
    {
        return $this->executeClassOrPropertyCommand(
            'create', $class, $property, $type, $linked
        );
    }

    /**
     * Executes a DELETE SQL query on the given class (= $from).
     *
     * @param   string $from
     * @return  Delete
     */
    public function delete($from)
    {
        $commandClass = $this->getCommandClass('delete');
        $this->command = new $commandClass($from);

        return $this->command;
    }

    /**
     * Drops a $class, or the $property in the given $class if
     * $property is specified.
     *
     * @param   string $class
     * @param   string $property
     * @return  mixed
     */
    public function drop($class, $property = null)
    {
        return $this->executeClassOrPropertyCommand('drop', $class, $property);
    }

    /**
     * Sets the fields to query.
     *
     * @param   array   $fields
     * @param   boolean $append
     * @return  Query
     */
    public function fields(array $fields, $append = true)
    {
        return $this->command->fields($fields, $append);
    }

    /**
     * Adds a from clause to the query.
     *
     * @param array   $target
     * @param boolean $append
     */
    public function from(array $target, $append = true)
    {
        return $this->command->from($target, $append);
    }

    /**
     * Returns the internal command.
     *
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Returns the raw SQL query statement.
     *
     * @return String
     */
    public function getRaw()
    {
        return $this->command->getRaw();
    }

    /**
     * Returns the tokens associated to the current query.
     *
     * @return array
     */
    public function getTokens()
    {
        return $this->command->getTokens();
    }

    /**
     * Converts the query into an GRANT with the given $permission.
     *
     * @param   string  $permission
     * @return  Grant
     */
    public function grant($permission)
    {
        $commandClass = $this->getCommandClass('grant');
        $this->command = new $commandClass($permission);

        return $this->command;
    }

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
    public function findReferences($rid, array $classes = array(), $append = true)
    {
        $commandClass = $this->getCommandClass('references.find');
        $this->command = new $commandClass($rid);
        $this->command->in($classes, $append);

        return $this->command;
    }


    /**
     * Sets the classes in which the query performs is operation.
     * For example a FIND REFERENCES uses the IN in order to find documents
     * referencing to a given document <code>in</code> N classes.
     *
     * @param   array   $in
     * @param   boolean $append
     * @return  mixed
     */
    public function in(array $in, $append = true)
    {
        return $this->command->in($in, $append);
    }

    /**
     * Creates a index
     *
     * @param   string $property
     * @param   string $class
     * @param   string $type
     * @return  Query
     */
    public function index($property, $type, $class = null)
    {
        $commandClass = $this->getCommandClass('index.create');
        $this->command = new $commandClass($property, $type, $class);

        return $this->command;
    }

    /**
     * Count the elements of the index $indexName.
     *
     * @param string $indexName
     */
    public function indexCount($indexName)
    {
        $commandClass = $this->getCommandClass('index.count');
        $this->command = new $commandClass($indexName);

        return $this->command;
    }

    /**
     * Puts a new entry in the index $indexName with the given $key and $rid.
     *
     * @param string $indexName
     * @param string $key
     * @param string $rid
     */
    public function indexPut($indexName, $key, $rid)
    {
        $commandClass = $this->getCommandClass('index.put');
        $this->command = new $commandClass($indexName, $key, $rid);

        return $this->command;
    }

    /**
     * Removes the index $indexName with the given $key/$rid.
     *
     * @param string $indexName
     * @param string $key
     * @param string $rid
     */
    public function indexRemove($indexName, $key, $rid = null)
    {
        $commandClass = $this->getCommandClass('index.remove');
        $this->command = new $commandClass($indexName, $key, $rid);

        return $this->command;
    }

    /**
     * Rebuild the index $indexName
     *
     * @param string $indexName
     */
    public function rebuild($indexName)
    {
        $commandClass = $this->getCommandClass('index.rebuild');
        $this->command = new $commandClass($indexName);

        return $this->command;
    }

    /**
     * Converts the query into an INSERT.
     *
     * @return Query
     */
    public function insert()
    {
        $commandClass = $this->getCommandClass('insert');
        $this->command = new $commandClass;

        return $this->command;
    }

    /**
     * Inserts the INTO clause to a query.
     *
     * @param  string $target
     * @return Query
     */
    public function into($target)
    {
        return $this->command->into($target);
    }

    /**
     * Adds a limit to the current query.
     *
     * @return $this
     */
    public function limit($limit)
    {
        return $this->command->limit($limit);
    }

    /**
     * Adds a skip clause to the current query.
     *
     * @return $this
     */
    public function skip($records)
    {
        return $this->command->skip($records);
    }

    /**
     * Sets the internal command to a LINK, which is capable to create a
     * reference from the $property of $class, with a given $alias.
     * You can specify if the link is one-* or two-way with the $inverse
     * parameter.
     *
     * @param  string  $class
     * @param  string  $property
     * @param  string  $alias
     * @param  boolean $inverse
     * @return Link
     */
    public function link($class, $property, $alias, $inverse = false)
    {
        $commandClass = $this->getCommandClass('link');
        $this->command = new $commandClass($class, $property, $alias, $inverse);

        return $this->command;
    }

    public function lookup($indexName)
    {
        $commandClass = $this->getCommandClass('index.lookup');
        $this->command = new $commandClass($indexName);

        return $this->command;
    }

    /**
     * Sets the ON clause of a query.
     *
     * @param  string $on
     * @return Query
     */
    public function on($on)
    {
        return $this->command->on($on);
    }

    /**
     * Orders the query.
     *
     * @param array   $order
     * @param boolean $append
     * @param boolean $first
     */
    public function orderBy($order, $append = true, $first = false)
    {
        return $this->command->orderBy($order, $append, $first);
    }

    /**
     * Adds an OR clause to the query.
     *
     * @param string $condition
     * @param mixed  $value
     */
    public function orWhere($condition, $value = null)
    {
        return $this->command->orWhere($condition, $value);
    }

    /**
     * Removes a link from a link-set|list.
     *
     * @param  array   $updates
     * @param  string  $class
     * @param  boolean $append
     * @return Remove
     */
    public function remove(array $updates, $class, $append = true)
    {
        $commandClass = $this->getCommandClass('update.remove');
        $this->command = new $commandClass($updates, $class, $append);

        return $this->command;
    }

    /**
     * Resets the WHERE conditions.
     *
     * @rerurn mixed
     */
    public function resetWhere()
    {
        $this->command->resetWhere();

        return $this->command;
    }

    /**
     * Converts the query into an REVOKE with the given $permission.
     *
     * @param  string $permission
     * @return Revoke
     */
    public function revoke($permission)
    {
        $commandClass = $this->getCommandClass('revoke');
        $this->command = new $commandClass($permission);

        return $this->command;
    }

    /**
     * Adds an array of fields into the select part of the query.
     *
     * @param array   $projections
     * @param boolean $append
     */
    public function select(array $projections, $append = true)
    {
        return $this->command->select($projections, $append);
    }

    /**
     * Sets the type clause of a query.
     *
     * @param  string $type
     * @return Query
     */
    public function type($type)
    {
        return $this->command->type($type);
    }

    /**
     * Adds a subject to the query.
     *
     * @param  string $to
     * @return Query
     */
    public function to($to)
    {
        return $this->command->to($to);
    }

    /**
     * Truncates an entity.
     *
     * @param  string  $entity
     * @param  boolean $andCluster
     * @return Query
     */
    public function truncate($entity, $andCluster = false)
    {
        try {
            $validator = new RidValidator;
            $validator->check($entity);
            $commandClass = $this->getCommandClass('truncate.record');
        } catch (ValidationException $e) {
            $commandClass = $this->getCommandClass('truncate.class');

            if ($andCluster) {
                $commandClass = $this->getCommandClass('truncate.cluster');
            }
        }

        $this->command = new $commandClass($entity);

        return $this->command;
    }

    /**
     * Sets the values to be inserted into the current query.
     *
     * @param  array   $values
     * @param  boolean $append
     * @return Insert
     */
    public function values(array $values, $append = true)
    {
        return $this->command->values($values, $append);
    }

    /**
     * Removes a index
     *
     * @param  string $property
     * @param  string $class
     * @return Query
     */
    public function unindex($property, $class = null)
    {
        $commandClass = $this->getCommandClass('index.drop');
        $this->command = new $commandClass($property, $class);

        return $this->command;
    }

    /**
     * Changes the internal command to an PUT, setting the class to update
     * and the values to be written.
     *
     * @param  string $class
     * @return Command
     */
    public function put(array $values, $class, $append = true)
    {
        $commandClass  = $this->getCommandClass('update.put');
        $this->command = new $commandClass($values, $class, $append);

        return $this->command;
    }

    /**
     * Checks whether the current query returns records that can be hydrated
     *
     * @return boolean
     */
    public function canHydrate()
    {
        return $this->getCommand()->canHydrate();
    }

    /**
     * Changes the internal command to an UPDATE, setting the class to update.
     *
     * @param  string $class
     * @return Command
     */
    public function update($class)
    {
        $commandClass = $this->getCommandClass('update');
        $this->command = new $commandClass($class);

        return $this->command;
    }

    /**
     * Adds the WHERE clause.
     *
     * @param string $condition
     * @param mixed  $value
     */
    public function where($condition, $value = null)
    {
        return $this->command->where($condition, $value);
    }

    /**
     * Returns on of the commands that belong to the query.
     *
     * @param  string $id
     * @return mixed
     */
    protected function getCommandClass($id)
    {
        if (isset($this->commands[$id])) {
            return $this->commands[$id];
        }

        throw new Exception(sprintf("command %s not found in %s", $id, get_called_class()));
    }

    /**
     * Sets the right class command based on the $action.
     *
     * @param string $action
     * @param string $class
     */
    protected function manageClass($action, $class)
    {
        $commandClass = $this->getCommandClass("class." . $action);
        $this->command = new $commandClass($class);

        return $this->command;
    }

    /**
     * Sets the right property command based on the $action.
     *
     * @param string $action
     * @param string $class
     * @param string $property
     */
    protected function manageProperty($action, $class, $property, $type = null, $linked = null)
    {
        $commandClass = $this->getCommandClass("property." . $action);
        $this->command = new $commandClass($property, $type, $linked);
        $this->command->on($class);

        return $this->command;
    }

    /**
     * Executes a class or property command checking if the $property parameter
     * is specified.
     * If none,  class command is executed.
     *
     * @param  string $action
     * @param  string $class
     * @param  string $property
     * @param  string $type
     * @param  string $linked
     * @return mixed
     */
    protected function executeClassOrPropertyCommand($action, $class, $property = null, $type = null, $linked = null)
    {
        if ($property) {
            return $this->manageProperty($action, $class, $property, $type, $linked);
        }

        return $this->manageClass($action, $class);
    }

    /**
     * Sets the internal command classes to use
     *
     * @param  array $commands
     * @return true
     */
    protected function setCommands(array $commands)
    {
        $this->commands = array_merge($this->commands, $commands);

        return true;
    }

    /**
     * Returns the raw SQL statement
     */
    public function __toString()
    {
        return $this->getRaw();
    }
}
