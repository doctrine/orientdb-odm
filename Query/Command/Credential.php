<?php

/**
 * Credential class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Formatter;
use Orient\Query\Command;

abstract class Credential extends Command
{
  public function setPermission($permission)
  {
    $this->setToken('Permission', array($permission), false);
  }

  public function on($resource)
  {
    $this->setToken('Resource', array($resource), false);
  }

  public function to($role)
  {
    $this->setToken('Role', array($role), false);
  }
}
