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

use Doctrine\OrientDB\Query\Query;

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
     * @param Query $query.
     * @param string $fetchPlan Optional fetch plan for the query.
     * @return BindingResultInterface
     */
    public function execute(Query $query, $fetchPlan = null);


    /**
     * Returns the name of the database the binding is
     * currently using.
     *
     * @return string
     */
    public function getDatabaseName();


    /**
     * Retrieves details regarding the specified database.
     *
     * @api
     * @param string $database
     * @return BindingResultInterface
     */
    public function getDatabase($database = null);
}
