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
  
  protected function setClass($class)
  {
    $this->setToken('Class', $class);
  }
}

