<?php

/**
 * Property interface
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Query\Command;

interface Property
{
  public function __construct($property);

  public function on($class);
}