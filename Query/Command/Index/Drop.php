<?php

/**
 * Drop class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command;

class Drop extends Command
{
  const SCHEMA = "DROP INDEX :Class.:Property";

  public function drop($class, $property)
  {
    $this->setToken('Class', array($class), false);
    $this->setToken('Property', array($property), false);
  }
}

