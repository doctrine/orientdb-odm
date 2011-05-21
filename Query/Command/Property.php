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

  public function on($class)
  {
    $this->setToken('Class', $class);

    return $this;
  }
  
  protected function setProperty($property)
  {
    $this->setToken('Property', $property);
  }
}

