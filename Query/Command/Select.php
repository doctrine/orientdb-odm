<?php

/**
 * Select class, to build SELECT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Exception\Query\Command as CommandException;
use Orient\Contract\Query\Formatter;
use Orient\Query\Command;
use Orient\Contract\Query\Command\Select as SelectInterface;

class Select extends Command implements SelectInterface
{  
  const SCHEMA          =
    "SELECT :Projections FROM :Target :Where :OrderBy :Limit :Range"
  ;

  /**
   * Builds a Select object injecting the $target into the FROM clause.
   *
   * @param array $target
   */
  public function __construct(array $target = NULL)
  {
    parent::__construct();

    if ($target)
    {
      $this->setToken('Target', $target);
    }
  }

  /**
   * Sets the fields to select.
   *
   * @param array   $projections
   * @param boolean $append
   */
  public function select(array $projections, $append = true)
  {
    $this->setToken('Projections', $projections, $append);
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
    $this->setToken('OrderBy', array($order), $append, $first);
  }

  /**
   * Sets a limit to the SELECT.
   *
   * @param integer $limit
   */
  public function limit($limit)
  {
    $this->setToken('Limit', array((int) $limit), false);
  }

  /**
   * Adds the range to the select.
   *
   * @param integer $limit
   */
  public function range($left = NULL, $right = NULL)
  {
    $range  = array();
    $params = array('left', 'right');

    foreach ($params as $param)
    {
      if ($$param)
      {
        $range[$param]   = $$param;
      }
      elseif ($$param === false)
      {
        $range[$param]   = NULL;
      }
    }

    $this->setToken('Range', $range);
  }
}

