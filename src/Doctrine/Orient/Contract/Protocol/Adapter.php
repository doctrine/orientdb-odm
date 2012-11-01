<?php

/*
 * This file is part of the Doctrine\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface Adapter defines the methods a protocol binding for Doctrine\Orient needs to
 * implement for interoperability with the libraries that want to rely on it.
 *
 * @package     Doctrine\Orient
 * @subpackage  Contract
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\Orient\Contract\Protocol;

interface Adapter
{
    /**
     * Executes a command against Doctrine\OrientDB thorugh the protocol binding,
     * returning mixed feedback or throwing an exception in case of error.
     *
     * @param  string $command SQL-like command to execute
     * @throws Doctrine\Orient\Exception\Query\SQL\Invalid
     * @throws Doctrine\Orient\Exception\Http\Response\Void
     * @return boolean
     */
    public function execute($sql);

    /**
     * When calling ->execute() with the $return set to true, it might happen
     * that OrientDB, after a query, gives back you a result, like when doing a
     * SELECT, not when doing an UPDATE, for example.
     *
     * When OrientDB gives you a consistent response, it gets stored in an
     * internal variable of the adapter, and can be retrieved at any time.
     *
     * For example, you may want to know if ->execute('SELECT FROM ADDRESS') was
     * received properly by OrientDB, checking execute()'s return parameter,
     * then you may want to use the result of that SELECT in your code.
     *
     * Pseudo code:
     * <code>
     * if ($adapter->execute('SELECT...')) {
     *   foreach ($adapter->getResult() as $records) {
     *     ...
     *   }
     * }
     * </code>
     */
    public function getResult();
}
