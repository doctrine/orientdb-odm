<?php

/**
 * Revoke class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Credential;

use \Orient\Contract\Query\Formatter;

class Revoke extends \Orient\Query\Command\Credential
{
  const SCHEMA          =
    "REVOKE :Permission ON :Resource TO :Role"
  ;

  public function revoke($permission)
  {
    $this->setPermission($permission);
  }
}

