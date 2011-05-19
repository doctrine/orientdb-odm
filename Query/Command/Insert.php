<?php

/**
 * Insert class, to build INSERT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Formatter;
use Orient\Query\Command;

class Insert extends Command
{
  const SCHEMA          =
    "INSERT INTO :Target (:Fields) VALUES (:Values)"
  ;

  public function fields(array $fields, $append = true)
  {
    $this->setTokenValues('Fields', $fields, $append);
  }

  public function into($target)
  {
    $this->setToken('Target', $target);
  }

  public function values(array $values, $append = true)
  {
    $this->setTokenValues('Values', $values, $append);
  }
}

