<?php

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
    
    $this->setToken('Type', array($type), false);
    $this->setToken('Linked', array($linked), false);
  }
}

