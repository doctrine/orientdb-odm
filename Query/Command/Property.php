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
 * This is a central point to manage SQL statements dealing with properties.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Command\Property as PropertyInterface;
use Orient\Query\Command;

class Property extends Command implements PropertyInterface
{
  /**
   * Builds a new statement setting the $property to manipulate.
   *
   * @param <type> $property
   */
  public function __construct($property)
  {
    parent::__construct();
    
    $this->setProperty($property);
  }

  /**
   * Sets the class of the property.
   *
   * @param   string    $class
   * @return  Property
   */
  public function on($class)
  {
    $this->setToken('Class', $class);

    return $this;
  }

  /**
   * Sets the $property in the query.
   *
   * @param string $property
   */
  protected function setProperty($property)
  {
    $this->setToken('Property', $property);
  }
}

