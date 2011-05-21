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
 * This class lets you build SQL statements to create a class in OrientDB.
 *
 * @package    Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Query\Command\OClass;

use Orient\Contract\Query\Command\OClass as OClassInterface;
use Orient\Query\Command\OClass;

class Create extends OClass implements OClassInterface
{
  const SCHEMA = "CREATE CLASS :Class";
}

