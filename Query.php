<?php

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
  protected $commands = array();

  /**
   * Builds a query with the given $command on the given $target.
   *
   * @param array   $target
   * @param string  $command
   */
  public function __construct(array $commands)
  {
    foreach ($commands as $id => $command)
    {
      $this->commands[$id] = $command;
    }

    $this->command = $this->getCommand('select');
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
    $this->command = $this->getCommand('grant');
    $this->command->grant($permission);

    return $this;
  }

  /**
   * Converts the query into an INSERT.
   *
   * @return Query
   */
  public function insert()
  {
    $this->command = $this->getCommand('insert');

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
    $this->command = new Revoke();
    $this->command->revoke($permission);

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
  protected function getCommand($id)
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
    $this->command = $this->getCommand("class." . $action);
    $this->command->setClass($class);
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
    $this->command = $this->getCommand("property." . $action);
    $this->command->property($property, $type, $linked);
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
}

