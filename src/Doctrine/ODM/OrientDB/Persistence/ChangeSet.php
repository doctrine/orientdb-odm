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
    private $removals;

    public function __construct(array $updates, array $inserts, array $removals)
    {
        $this->updates = $updates;
        $this->inserts = $inserts;
        $this->removals = $removals;
    }

    /**
     * @return array
     */
    public function getInserts()
    {
        return $this->inserts;
    }

    /**
     * @return array
     */
    public function getRemovals()
    {
        return $this->removals;
    }

    /**
     * @return array
     */
    public function getUpdates()
    {
        return $this->updates;
    }
}