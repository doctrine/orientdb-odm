<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Query class to build queries execute by an OrientDB's protocol adapter.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient;

use Orient\Query\Command\Credential\Grant;
use Orient\Query\Command\Credential\Revoke;
use Orient\Query\Command\Insert;
use Orient\Query\Command\Select;
use Orient\Exception;
use Orient\Contract\Query\Command\Select as SelectInterface;
use Orient\Contract\Query\Command\Insert as InsertInterface;
use Orient\Contract\Query\Command\Grant as GrantInterface;
use Orient\Contract\Query\Command\Revoke as RevokeInterface;

class Query
{
    protected $command = NULL;
    protected $commands = array(
        'select'            => 'Orient\Query\Command\Select',
        'insert'            => 'Orient\Query\Command\Insert',
        'delete'            => 'Orient\Query\Command\Delete',
        'update'            => 'Orient\Query\Command\Update',
        'update.add'        => 'Orient\Query\Command\Update\Add',
        'update.remove'     => 'Orient\Query\Command\Update\Remove',
        'update.put'        => 'Orient\Query\Command\Update\Put',
        'grant'             => 'Orient\Query\Command\Credential\Grant',
        'revoke'            => 'Orient\Query\Command\Credential\Revoke',
        'class.create'      => 'Orient\Query\Command\OClass\Create',
        'class.drop'        => 'Orient\Query\Command\OClass\Drop',
        'class.alter'       => 'Orient\Query\Command\OClass\Alter',
        'references.find'   => 'Orient\Query\Command\Reference\Find',
        'property.create'   => 'Orient\Query\Command\Property\Create',
        'property.drop'     => 'Orient\Query\Command\Property\Drop',
        'property.alter'    => 'Orient\Query\Command\Property\Alter',
        'index.drop'        => 'Orient\Query\Command\Index\Drop',
        'index.create'      => 'Orient\Query\Command\Index\Create',
        'link'              => 'Orient\Query\Command\Link',
    );

    /**
     * Builds a query with the given $command on the given $target.
     *
     * @param array   $target
     * @param string  $command
     */
    public function __construct(array $target = NULL, array $commands = array())
    {
        $this->setCommands($commands);

        $commandClass = $this->getCommandClass('select');
        $this->command = new $commandClass($target);
    }

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
    public function andWhere($condition, $value = NULL)
    {
        return $this->command->andwhere($condition, $value);
    }

    public function create($class, $property = NULL, $type = NULL, $linked = NULL)
    {
        return $this->executeClassOrPropertyCommand('create', $class, $property, $type, $linked);
    }

    public function delete($from)
    {
        $commandClass = $this->getCommandClass('delete');
        $this->command = new $commandClass($from);

        return $this;
    }

    public function drop($class, $property = NULL)
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
     * Returns the raw SQL query.
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
     * Converts the query into an GRANT with $permission.
     *
     * @return Query
     */
    public function grant($permission)
    {
        $commandClass = $this->getCommandClass('grant');
        $this->command = new $commandClass($permission);

        return $this->command;
    }

    public function findReferences($rid, array $classes = array(), $append = true)
    {
        $commandClass = $this->getCommandClass('references.find');
        $this->command = new $commandClass($rid);
        $this->command->in($classes, $append);

        return $this->command;
    }

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
    public function index($property, $class = NULL, $type = NULL)
    {
        $commandClass = $this->getCommandClass('index.create');
        $this->command = new $commandClass($property, $class, $type);

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
     * @param   string $target
     * @return  Query
     */
    public function into($target)
    {
        return $this->command->into($target);
    }

    /**
     * Adds a limit to the current query.
     *
     * @return  $this
     */
    public function limit($limit)
    {
        return $this->command->limit($limit);
    }

    public function link($class, $property, $alias, $inverse = false)
    {
        $commandClass = $this->getCommandClass('link');
        $this->command = new $commandClass($class, $property, $alias, $inverse);

        return $this->command;
    }

    /**
     * Sets the ON clause of a query.
     *
     * @param   string $on
     * @return  Query
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
     * @param string  $condition
     * @param mixed   $value
     */
    public function orWhere($condition, $value = NULL)
    {
        return $this->command->orWhere($condition, $value);
    }

    /**
     * Resets the RANGE condition.
     */
    public function range($left = NULL, $right = NULL)
    {
        return $this->command->range($left, $right);
    }

    public function remove(array $updates, $class, $append = true)
    {
        $commandClass = $this->getCommandClass('update.remove');
        $this->command = new $commandClass($updates, $class, $append);

        return $this->command;
    }

    /**
     * Resets the WHERE conditions.
     */
    public function resetWhere()
    {
        $this->command->resetWhere();

        return $this;
    }

    /**
     * Converts the query into an REVOKE with $permission.
     *
     * @return Query
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
     * @param   string $type
     * @return  Query
     */
    public function type($type)
    {
        return $this->command->type($type);
    }

    /**
     * Adds a subject to the query.
     *
     * @param   string   $to
     * @return  Query
     */
    public function to($to)
    {
        return $this->command->to($to);
    }

    public function values(array $values, $append = true)
    {
        return $this->command->values($values, $append);
    }

    /**
     * Removes a index
     *
     * @param   string $property
     * @param   string $class
     * @return  Query
     */
    public function unindex($property, $class = NULL)
    {
        $commandClass = $this->getCommandClass('index.drop');
        $this->command = new $commandClass($property, $class);

        return $this->command;
    }

    public function put(array $values, $class, $append = true)
    {
        $commandClass = $this->getCommandClass('update.put');
        $this->command = new $commandClass($values, $class, $append);

        return $this->command;
    }

    public function update($class)
    {
        $commandClass = $this->getCommandClass('update');
        $this->command = new $commandClass($class);

        return $this->command;
    }

    /**
     * Adds the WHERE clause.
     *
     * @param string  $condition
     * @param mixed   $value
     */
    public function where($condition, $value = NULL)
    {
        return $this->command->where($condition, $value);
    }

    /**
     * Returns on of the commands that belong to the query.
     *
     * @param   string $id
     * @return  mixed
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
    protected function manageProperty($action, $class, $property, $type = NULL, $linked = NULL)
    {
        $commandClass = $this->getCommandClass("property." . $action);
        $this->command = new $commandClass($property, $type, $linked);
        $this->command->on($class);

        return $this->command;
    }

    protected function executeClassOrPropertyCommand($action, $class, $property = NULL, $type = NULL, $linked = NULL)
    {
        if ($property) {
            return $this->manageProperty($action, $class, $property, $type, $linked);
        } else {
            return $this->manageClass($action, $class);
        }
    }

    /**
     * Sets the internal command classes to use
     *
     * @param   array $commands
     * @return  true
     */
    protected function setCommands(array $commands)
    {
        $this->commands = array_merge($this->commands, $commands);

        return true;
    }
}
