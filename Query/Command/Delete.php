<?php

/**
 * Delete class
 *
 * @package     Orient
 * @subpackage  Query
 * @author      Alessandro nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class Delete extends Command
{
  const SCHEMA = "DELETE FROM :Class :Where";
  
  public function __construct($from)
  {
    parent::__construct();
    
    $this->setClass($from);
  }
  
  public function setClass($class)
  {
    $this->setToken('Class', $class);
  }
}

