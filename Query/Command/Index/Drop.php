<?php

/**
 * Drop class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Index;

use Orient\Query\Command\Index;

class Drop extends Index
{
  const SCHEMA = "DROP INDEX :Class.:Property";
}

