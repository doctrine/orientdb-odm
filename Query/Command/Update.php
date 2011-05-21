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
 * Update class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class Update extends Command
{
  const SCHEMA =
    "UPDATE :Class SET :Updates :Where"
  ;

  public function __construct($class)
  {
    parent::__construct();

    $this->setToken('Class', $class);
  }

  public function set(array $values, $append = true)
  {
    $this->setTokenValues('Updates', $values, $append);

    return $this;
  }
}

