<?php

/**
 * Drop class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Property;

use Orient\Query\Command;

class Drop extends Command
{
  const SCHEMA = "DROP PROPERTY :Class.:Property";

  public function property($property)
  {
    $this->setToken('Property', array($property), false);
  }

  public function on($class)
  {
    $this->setToken('Class', array($class), false);
  }
}

