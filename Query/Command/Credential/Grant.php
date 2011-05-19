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

  public function __construct($permission)
  {
    parent::__construct();
    
    $this->permission($permission);
  }
  
  public function permission($permission)
  {
    $this->setToken('Permission', array($permission), false);
  }
}
