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
 * Collection class
 *
 * @package    Doctrine\OrientDB
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\OrientDB\Foundation\Types\Rid;

use Doctrine\OrientDB\Foundation\Types\Rid;

class Collection extends Rid
{
    protected $rids;

    /**
     * Instatiates a new collection, setting the $rids belonging to it.
     *
     * @param Array $rids
     */
    public function __construct($rids)
    {
        $this->rids = $rids;
    }

    /**
     * Returns the rids associated to the collection.
     *
     * @return Array
     */
    public function getValue()
    {
        return $this->rids;
    }
}
