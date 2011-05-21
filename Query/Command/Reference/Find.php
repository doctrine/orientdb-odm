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
 * Find class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Reference;

use Orient\Contract\Query\Command\Reference\Find as FindInterface;
use Orient\Query\Command;

class Find extends Command implements FindInterface
{
  const SCHEMA = "FIND REFERENCES :Rid :ClassList";
  
  public function __construct($rid)
  {
    parent::__construct();
    
    $this->setRid($rid);
  }

  public function in(array $classes, $append = true)
  {
    $this->setTokenValues('ClassList', $classes, $append);

    return $this;
  }
  
  protected function setRid($rid)
  {
    $this->setToken('Rid', $rid);
  }
}

