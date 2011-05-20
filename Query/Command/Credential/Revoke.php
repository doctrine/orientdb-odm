<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Revoke class
 *
 * @package    Orient
 * @subpackage Query
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
  
  public function __construct($permission)
  {
    parent::__construct();
    
    $this->setPermission($permission);
  }
}

