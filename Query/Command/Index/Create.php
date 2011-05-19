<?php

/**
 * Create class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command\Index;

use Orient\Query\Command;

class Create extends Index
{
  const SCHEMA = "CREATE INDEX :Class.:Property";
}

