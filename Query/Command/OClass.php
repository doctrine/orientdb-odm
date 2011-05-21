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
 * This class is a central point to manage SQL statements dealing with
 * class manipulation in OrientDB.
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class OClass extends Command
{
  /**
   * Creates a new statement, setting the $class.
   *
   * @param string $class
   */
  public function __construct($class)
  {
    parent::__construct();
    
    $this->setClass($class);
  }

  /**
   * Sets the $class for the current query.
   *
   * @param   string $class
   * @return  void
   */
  protected function setClass($class)
  {
    return $this->setToken('Class', $class);
  }
}

