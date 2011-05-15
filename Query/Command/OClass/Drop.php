<?php

/**
 * Drop class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\OClass;

use Orient\Query\Command\OClass;

class Drop extends OClass
{
  const SCHEMA = "DROP CLASS :Class";
}
