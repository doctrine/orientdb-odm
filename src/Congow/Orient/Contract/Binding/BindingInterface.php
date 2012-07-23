<?php

/*
 * This file is part of the Congow\Orient package.
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
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Congow\Orient\Contract\Binding;

interface BindingInterface
{
    /**
     * Executes an SQL query on the server.
     *
     * The second argument specifies when to use COMMAND or QUERY as the
     * underlying command. When passing a string as the second argument,
     * this string should be used as a fetch plan.
     *
     * @param string $sql Raw SQL query.
     * @param mixed $results or use the specified fetchplan.
     * @return BindingResultInterface
     */
    public function execute($sql, $results = false);
}
