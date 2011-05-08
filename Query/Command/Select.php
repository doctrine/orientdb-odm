<?php

/**
 * Select class, to build SELECT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use \Orient\Exception\Query\Command as CommandException;

class Select extends \Orient\Query\Command
{
  protected $tokens     = array();
  
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
    $this->statement  = self::SCHEMA;
    $this->tokens     = $this->getTokens();

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
}

