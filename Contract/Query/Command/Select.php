<?php

/**
 * Select interface
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Contract\Query\Command;

interface Select
{
  public function __construct(array $target);

  public function select(array $projections, $append);

  public function orderBy($order, $append, $first);

  public function limit($limit);

  public function range($left, $right);
}

