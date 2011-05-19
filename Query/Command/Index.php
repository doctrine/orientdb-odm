<?php

/**
 * Index class
 *
 * @package     Orient
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class Index extends Command
{  
  public function __construct($class, $property)
  {
    parent::__construct();
    
    $this->setToken('Class', $class);
    $this->setToken('Property', $property);
  }
}