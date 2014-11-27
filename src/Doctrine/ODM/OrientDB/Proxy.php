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
 * Proxy class
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Doctrine\ODM\OrientDB;

use Doctrine\ODM\OrientDB\Proxy\AbstractProxy;

class Proxy extends AbstractProxy
{
    protected $manager;
    protected $rid;
    protected $record;

    /**
     * Istantiates a new Proxy.
     *
     * @param Manager $manager
     * @param string $rid
     */
    public function __construct(Manager $manager, $rid)
    {
        $this->manager = $manager;
        $this->rid = $rid;
    }

    /**
     * Returns the record loaded with the Mapper.
     *
     * @return object
     */
    public function __invoke()
    {
        if (!$this->record) {
            $this->record = $this->getManager()->find($this->getRid());
        }

        return $this->record;
    }

    /**
     * Returns the RID of the record to find.
     *
     * @return string
     */
    public function getRid()
    {
        return $this->rid;
    }
}
