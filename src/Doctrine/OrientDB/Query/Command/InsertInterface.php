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
 * Insert interface provides common method to deal with document insertions
 * in Doctrine\OrientDB.
 *
 * @package    Doctrine\OrientDB
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Query\Command;

interface InsertInterface
{
    /**
     * Sets the $fields that are going to be inserted.
     * The $append parameter is used in order to preserve/discard already-set
     * fields.
     *
     * @param   array   $fields
     * @param   boolean $append
     * @return  Insert
     */
    public function fields(array $fields, $append = true);

    /**
     * Sets the $target cluster in which the new document will be created.
     *
     * @param   string  $target
     * @return  Insert
     */
    public function into($target);

    /**
     * Sets the $values to be inserted in the document created with the INSERT
     * statement.
     * The $append parameter is used in order to preserve/discard already-set
     * fields.
     *
     * @param   array   $values
     * @param   boolean $append
     * @return  Insert
     */
    public function values(array $values, $append = true);
}
