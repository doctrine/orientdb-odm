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
 * This class handles the SQL statement to generate an index into the DB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command\Index;

use Orient\Query\Command;

class Lookup extends Index
{
  const SCHEMA = "SELECT FROM index::Index :Where";
  
  /**
   * Builds a new statement, setting the $class.
   *
   * @param string $index
   * @param string $where
   */
  public function __construct($index)
  {
    parent::__construct($index);
    $this->setToken('Index',$index);
  } 
}

