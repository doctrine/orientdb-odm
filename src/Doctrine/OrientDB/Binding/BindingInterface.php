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
 * This interface is the foundation of the library as it enables to implement
 * classes that can peform requests to OrienDB using different protocols or
 * backends.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Binding
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Doctrine\OrientDB\Binding;

interface BindingInterface
{
    const LANGUAGE_SQLPLUS = 'sql';
    const LANGUAGE_GREMLIN = 'gremlin';

    /**
     * Executes an SQL query on the server.
     *
     * The second argument specifies when to use COMMAND or QUERY as the
     * underlying command.
     *
     * @param string $sql Raw SQL query.
     * @param bool $results Whether to use `command` or `query` for the underlying command.
     * @param string $fetchPlan Optional fetch plan for the query.
     * @return BindingResultInterface
     */
    public function execute($sql, $return = false, $fetchPlan = null);
}
