<?php

/**
 * Grant class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Query\Command;

use \Orient\Contract\Query\Formatter;

class Grant extends \Orient\Query\Command
{
  const SCHEMA          =
    "GRANT :Permission ON :Resource TO :Role"
  ;

  /**
   * @param array $target
   * @param Formatter $formatterClass
   */
  public function __construct(array $target = NULL, Formatter $formatterClass = NULL)
  {
    parent::__construct($target, $formatterClass);

    $this->statement  = self::SCHEMA;
    $this->tokens     = $this->getTokens();
  }

  public function grant($permission)
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
