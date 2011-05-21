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
 * Credential class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Contract\Query\Command\Credential as CredentialInterface;
use Orient\Query\Command;

abstract class Credential extends Command implements CredentialInterface
{
  public function __construct($permission)
  {
    parent::__construct();

    $this->permission($permission);
  }

  public function permission($permission)
  {
    $this->setToken('Permission', $permission);

    return $this;
  }

  public function on($resource)
  {
    $this->setToken('Resource', $resource);

    return $this;
  }

  public function to($role)
  {
    $this->setToken('Role', $role);

    return $this;
  }
}
