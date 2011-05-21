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
 * Put class
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\Update;

use Orient\Query\Command\Update;

class Put extends Update
{
  const SCHEMA =
    "UPDATE :Class PUT :MapUpdates :Where"
  ;

  public function __construct(array $values, $class, $append = true)
  {
    parent::__construct($class);

    $this->setTokenValues('MapUpdates', $values, $append);
  }
}

