<?php

/**
 * Insert class, to build INSERT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use \Orient\Exception\Query\Command as CommandException;
use \Orient\Contract\Query\Formatter;

class Insert extends \Orient\Query\Command
{
  const SCHEMA          =
    "INSERT INTO :Target (:Fields) VALUES (:Values)"
  ;

  /**
   * Builds a Select object injecting the $target into the FROM clause.
   *
   * @param array $target
   */
  public function __construct(array $target = NULL, Formatter $formatterClass = NULL)
  {
    parent::__construct($target, $formatterClass);

    $this->statement  = self::SCHEMA;
    $this->tokens     = $this->getTokens();
  }

  public function fields(array $fields, $append = true)
  {
    $this->setToken('Fields', $fields, $append);
  }

  public function into($target)
  {
    $this->setToken('Target', array($target), false);
  }

  public function values(array $values, $append)
  {
    $this->setToken('Values', $values, true);
  }
}

