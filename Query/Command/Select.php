<?php

/**
 * Select command class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use \Orient\Exception\Query\Command as CommandException;

class Select extends \Orient\Query\Command
{
  protected $statement  = NULL;
  protected $tokens     = array();
  
  const SCHEMA          =
    "SELECT :Projections FROM :Target :Where :OrderBy :Limit :Range"
  ;

  public function __construct(array $target = NULL)
  {
    $this->statement  = self::SCHEMA;
    $this->tokens     = $this->getTokens();

    if ($target)
    {
      $this->setToken('Target', $target);
    }
  }

  public function select(array $projections, $append = true)
  {
    $this->setToken('Projections', $projections, $append);
  }
}

