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
 * Link class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command;

use Orient\Query\Command;

class Link extends Command
{
  const SCHEMA =
    "CREATE LINK :Name FROM :SourceClass.:SourceProperty TO :DestinationClass.:DestinationProperty :Inverse"
  ;

  public function  __construct($class, $property, $alias, $inverse = false)
  {
    parent::__construct();

    $this->setToken('SourceClass', $class);
    $this->setToken('SourceProperty', $property);
    $this->setToken('Name', $alias);

    if ($inverse)
    {
      $this->setToken('Inverse', 'INVERSE');
    }
  }

  public function to($class, $property)
  {
    $this->setToken('DestinationClass', $class);
    $this->setToken('DestinationProperty', $property);

    return $this;
  }
}

