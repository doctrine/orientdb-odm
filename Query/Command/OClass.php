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
  
  protected function setClass($class)
  {
    return $this->setToken('Class', $class);
  }
}

