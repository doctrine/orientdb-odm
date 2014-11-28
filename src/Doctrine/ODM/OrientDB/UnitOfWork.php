<?php

namespace Doctrine\ODM\OrientDB;


use Doctrine\ODM\OrientDB\Mapper\Hydration\Hydrator;

/**
 * Class UnitOfWork
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Tamás Millián <tamas.millian@gmail.com>
 */
class UnitOfWork
{
    private $manager;
    private $hydrator;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns the manager the UnitOfWork is attached to
     *
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     *
     * Lazily instantiates and returns the Hydrator
     *
     * @return Hydrator
     */
    public function getHydrator()
    {
        if (! $this->hydrator) {
            $this->hydrator = new Hydrator($this->manager);
        }

        return $this->hydrator;
    }
} 