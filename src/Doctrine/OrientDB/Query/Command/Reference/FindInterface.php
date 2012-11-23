<?php

/*
 * This file is part of the Doctrine\OrientDB package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This interface is responsible of tracing bounds for the Find references
 * SQL command.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command\Reference;

interface FindInterface
{
  /**
   * Sets a list of $classes in which you can look for object's references.
   * The $append parameter is used to determine wheter to append or overwrite
   * the classes to existing ones ( usually set with a fluent interface ).
   *
   * @return Find
   */
  public function in(array $classes, $append);
}
