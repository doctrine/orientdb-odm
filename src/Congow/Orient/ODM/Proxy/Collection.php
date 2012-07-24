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
 * Collection class
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM\Proxy;

use Congow\Orient\ODM\Manager;
use Congow\Orient\ODM\Proxy\AbstractProxy;

class Collection extends AbstractProxy
{
    protected $manager;
    protected $rids;
    protected $collection;

    /**
     * Instantiates a new Proxy collection.
     *
     * @param Manager   $manager
     * @param array     $rids
     */
    function __construct(Manager $manager, Array $rids)
    {
        $this->manager = $manager;
        $this->rids    = $rids;
    }

    /**
     * Returns the array of records associated with this proxy.
     *
     * @return Array
     */
    public function __invoke()
    {
        if ($this->collection) {
            return $this->collection;
        } else {
            $this->collection = $this->getManager()->findRecords($this->getRids());

            return $this->collection;
        }
    }

    /**
     * Returns the RIDs to find.
     *
     * @return array
     */
    protected function getRids()
    {
        return $this->rids;
    }
}
