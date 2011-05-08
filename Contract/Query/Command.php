<?php

/**
 * Command interface, a common interface for all the SQL commands executable
 * by OrientDB.
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Query;

interface Command
{
  const SCHEMA = NULL;

  public function __construct(array $target = NULL);

  public function getRaw();
}

