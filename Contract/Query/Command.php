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
 * Command interface, a common interface for all the SQL commands executable
 * by OrientDB.
 *
 * @package    Orient
 * @subpackage Contract
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @todo       Add all the query methods
 */

namespace Orient\Contract\Query;

use \Orient\Contract\Query\Formatter;

interface Command
{
    const SCHEMA = NULL;

    public function getRaw();

    public static function getTokens();

    public function from(array $target, $append = true);

    public function getTokenValue($token);

    public function where($condition, $value = NULL, $append = false, $clause = "WHERE");
}
