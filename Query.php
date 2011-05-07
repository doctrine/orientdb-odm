<?php

/**
 * Query class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient;

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
    $command = "\\Orient\\Query\\Command\\" . ucfirst($command);

    $this->command = new $command($target);
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
   * Adds an OR clause to the query.
   *
   * @param string  $condition
   * @param mixed   $value
   */
  public function orWhere($condition, $value = NULL)
  {
    $this->command->where($condition, $value, true, "OR");
  }

  /**
   * Resets the WHERE conditions.
   */
  public function resetWhere()
  {
    $this->command->resetWhere();
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
  }
}

