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
use Orient\Contract\Query\Command\Select  as SelectInterface;
use Orient\Contract\Query\Command\Insert  as InsertInterface;
use Orient\Contract\Query\Command\Grant   as GrantInterface;
use Orient\Contract\Query\Command\Revoke  as RevokeInterface;

class Query
{
  protected $command  = NULL;
  protected $commands = array(
    'select'          =>  'Orient\Query\Command\Select',
    'insert'          =>  'Orient\Query\Command\Insert',
    'delete'          =>  'Orient\Query\Command\Delete',
    'grant'           =>  'Orient\Query\Command\Credential\Grant',
    'revoke'          =>  'Orient\Query\Command\Credential\Revoke',
    'class.create'    =>  'Orient\Query\Command\OClass\Create',
    'class.drop'      =>  'Orient\Query\Command\OClass\Drop',
    'references.find' =>  'Orient\Query\Command\Reference\Find',
    'property.create' =>  'Orient\Query\Command\Property\Create',
    'property.drop'   =>  'Orient\Query\Command\Property\Drop',
    'index.drop'      =>  'Orient\Query\Command\Index\Drop',
    'index.create'    =>  'Orient\Query\Command\Index\Create',
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

    $commandClass   = $this->getCommandClass('select');
    $this->command  = new $commandClass($target);
  }

  /**
   * Adds a where condition to the query.
   *
   * @param string  $condition
   * @param mixed   $value
   */
  public function andWhere($condition, $value = NULL)
  {
    $this->command->where($condition, $value, true, "AND");

    return $this;
  }

  public function create($class, $property = NULL, $type = NULL, $linked = NULL)
  {
    $this->executeClassOrPropertyCommand('create', $class, $property, $type, $linked);

    return $this;
  }
  
  public function delete($from)
  {
    $commandClass   = $this->getCommandClass('delete');
    $this->command  = new $commandClass($from);
    
    return $this;
  }

  public function drop($class, $property = NULL)
  {
    $this->executeClassOrPropertyCommand('drop', $class, $property);

    return $this;
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
    $this->command->fields($fields, $append);

    return $this;
  }

  /**
   * Adds a from clause to the query.
   *
   * @param array   $target
   * @param boolean $append
   */
  public function from(array $target, $append = true)
  {
    $this->command->from($target, $append);

    return $this;
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
    $command = $this->command;

    return $command::getTokens();
  }

  /**
   * Converts the query into an GRANT with $permission.
   *
   * @return Query
   */
  public function grant($permission)
  {    
    $commandClass   = $this->getCommandClass('grant');
    $this->command  = new $commandClass($permission);

    return $this;
  }

  public function findReferences($rid, array $classes = array(), $append = true)
  {
    $commandClass   = $this->getCommandClass('references.find');
    $this->command  = new $commandClass($rid);
    $this->command->in($classes, $append);

    return $this;
  }
  
  public function in(array $in, $append = true)
  {
    $this->command->in($in, $append);

    return $this;
  }

  /**
   * Creates a index
   *
   * @param   string $class
   * @param   string $property
   * @return  Query
   */
  public function index($class, $property)
  {
    $commandClass = $this->getCommandClass('index.create');
    $this->command  = new $commandClass($class, $property);

    return $this;
  }

  /**
   * Converts the query into an INSERT.
   *
   * @return Query
   */
  public function insert()
  {
    $commandClass   = $this->getCommandClass('insert');
    $this->command  = new $commandClass;

    return $this;
  }

  /**
   * Inserts the INTO clause to a query.
   *
   * @param   string $target
   * @return  Query
   */
  public function into($target)
  {
    $this->command->into($target);

    return $this;
  }

  /**
   * Adds a limit to the current query.
   *
   * @return  $this
   */
  public function limit($limit)
  {
    $this->command->limit($limit);

    return $this;
  }

  /**
   * Sets the ON clause of a query.
   *
   * @param   string $on
   * @return  Query
   */
  public function on($on)
  {
    $this->command->on($on);

    return $this;
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
    $this->command->orderBy($order, $append, $first);

    return $this;
  }

  /**
   * Adds an OR clause to the query.
   *
   * @param string  $condition
   * @param mixed   $value
   */
  public function orWhere($condition, $value = NULL)
  {
    $this->command->where($condition, $value, true, "OR");

    return $this;
  }

  /**
   * Resets the RANGE condition.
   */
  public function range($left = NULL, $right = NULL)
  {
    $this->command->range($left, $right);

    return $this;
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
    $commandClass   = $this->getCommandClass('revoke');
    $this->command  = new $commandClass($permission);

    return $this;
  }

  /**
   * Adds an array of fields into the select part of the query.
   *
   * @param array   $projections
   * @param boolean $append
   */
  public function select(array $projections, $append = true)
  {
    $this->command->select($projections, $append);

    return $this;
  }

  /**
   * Adds a subject to the query.
   *
   * @param   string   $to
   * @return  Query
   */
  public function to($to)
  {
    $this->command->to($to);

    return $this;
  }

  public function values(array $values, $append = true)
  {
    $this->command->values($values, $append);
    
    return $this;
  }

  /**
   * Removes a index
   *
   * @param   string $class
   * @param   string $property
   * @return  Query
   */
  public function unindex($class, $property)
  {
    $commandClass   = $this->getCommandClass('index.drop');
    $this->command  = new $commandClass($class, $property);

    return $this;
  }

  /**
   * Adds the WHERE clause.
   *
   * @param string  $condition
   * @param mixed   $value
   */
  public function where($condition, $value = NULL)
  {
    $this->command->where($condition, $value);

    return $this;
  }

  /**
   * Returns on of the commands that belong to the query.
   *
   * @param   string $id
   * @return  mixed
   */
  protected function getCommandClass($id)
  {
    if (isset($this->commands[$id]))
    {
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
    $commandClass   = $this->getCommandClass("class." . $action);
    $this->command  = new $commandClass($class);
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
    $commandClass   = $this->getCommandClass("property." . $action);
    $this->command  = new $commandClass($property, $type, $linked);
    $this->command->on($class);
    
  }

  protected function executeClassOrPropertyCommand($action, $class, $property = NULL, $type = NULL, $linked = NULL)
  {
    if ($property)
    {
      $this->manageProperty($action, $class, $property, $type, $linked);
    }
    else
    {
      $this->manageClass($action, $class);
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

