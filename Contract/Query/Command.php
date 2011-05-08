<?php

/**
 * Command interface, a common interface for all the SQL commands executable
 * by OrientDB.
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @todo       Add all the query methods
 */

namespace Orient\Contract\Query;

use \Orient\Contract\Query\Formatter;

interface Command
{
  const SCHEMA = NULL;

  public function __construct(array $target = NULL, Formatter $formatter = NULL);

  public function getRaw();
}

