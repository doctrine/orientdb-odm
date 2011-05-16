<?php

/**
 * Create class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command;

class Create extends Command
{
  const SCHEMA = "CREATE INDEX :Class.:Property";

  public function create($class, $property)
  {
    $this->setToken('Class', array($class), false);
    $this->setToken('Property', array($property), false);
  }
}

