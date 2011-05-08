<?php

/**
 * Query class to build queries execute by an OrientDB's protocol adapter.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient;

use \Orient\Query\Command\Insert;
use \Orient\Query\Command\Select;

class Query
{
  protected $command = NULL;

  /**
   * Builds a query with the given $command on the given $target.
   *
   * @param array   $target
   * @param string  $command
   */
  public function __construct(array $target = NULL, $command = 'select')
  {
    $this->command  = new Select($target);

    return $this;
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
   * Converts the query into an INSERT.
   *
   * @return Query
   */
  public function insert()
  {
    $this->command = new Insert();

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
}

