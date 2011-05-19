<?php

/**
 * Property class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class Property extends Command
{
  public function __construct($property)
  {
    parent::__construct();
    
    $this->setProperty($property);
  }
  
  public function setProperty($property)
  {
    $this->setToken('Property', $property);
  }

  public function on($class)
  {
    $this->setToken('Class', $class);
  }
}

