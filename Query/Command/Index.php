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
 * This class manages indexes on OrientDB.
 *
 * @package     Orient
 * @subpackage  Query
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Command\Index as IndexInterface;
use Orient\Query\Command;

class Index extends Command implements IndexInterface
{
  /**
   * Creates a new statements to manage indexes on the $property of the given
   * $class.
   *
   * @param string $property
   * @param string $class
   */
  public function __construct($property, $class = NULL)
  {
    parent::__construct();
    
    $this->setToken('IndexClass', $class);
    $this->setToken('Property', $property);
  }
}