<?php

/**
 * Grant class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Credential;

use Orient\Contract\Query\Formatter;
use Orient\Query\Command\Credential;

class Grant extends Credential
{
  const SCHEMA          =
    "GRANT :Permission ON :Resource TO :Role"
  ;

  public function grant($permission)
  {
    $this->setToken('Permission', array($permission), false);
  }
}
