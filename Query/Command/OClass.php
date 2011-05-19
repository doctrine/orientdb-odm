<?php

/**
 * OClass class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class OClass extends Command
{
  public function __construct($class)
  {
    parent::__construct();
    
    $this->setClass($class);
  }
  
  public function setClass($class)
  {
    return $this->setToken('Class', $class);
  }
}

