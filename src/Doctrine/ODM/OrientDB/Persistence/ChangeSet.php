<?php

namespace Doctrine\ODM\OrientDB\Persistence;


/**
 * Class ChangeSet
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class ChangeSet
{
    private $updates;
    private $inserts;

    public function __construct(array $updates, array $inserts)
    {
        $this->updates = $updates;
        $this->inserts = $inserts;
    }

    public function getUpdates()
    {
        return $this->updates;
    }

    public function getInserts()
    {
        return $this->inserts;
    }
} 