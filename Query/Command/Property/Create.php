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
 * Create class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Property;

use Orient\Query\Command\Property;

class Create extends Property
{
  const SCHEMA = "CREATE PROPERTY :Class.:Property :Type :Linked";

  public function __construct($property, $type = NULL, $linked = NULL)
  {
    parent::__construct($property);
    
    $this->setToken('Type', $type);
    $this->setToken('Linked', $linked);
  }
}

