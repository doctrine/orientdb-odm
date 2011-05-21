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
 * Insert class, to build INSERT commands for OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Formatter;
use Orient\Contract\Query\Command\Insert as InsertInterface;
use Orient\Query\Command;

class Insert extends Command implements InsertInterface
{
  const SCHEMA          =
    "INSERT INTO :Target (:Fields) VALUES (:Values)"
  ;

  public function fields(array $fields, $append = true)
  {
    $this->setTokenValues('Fields', $fields, $append);

    return $this;
  }

  public function into($target)
  {
    $this->setToken('Target', $target);

    return $this;
  }

  public function values(array $values, $append = true)
  {
    $this->setTokenValues('Values', $values, $append);

    return $this;
  }
}

