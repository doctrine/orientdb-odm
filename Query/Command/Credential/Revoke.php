<?php

/**
 * Revoke class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Credential;

use Orient\Contract\Query\Formatter;
use Orient\Query\Command\Credential;

class Revoke extends Credential
{
  const SCHEMA          =
    "REVOKE :Permission ON :Resource TO :Role"
  ;

  public function revoke($permission)
  {
    $this->setPermission($permission);
  }
}

