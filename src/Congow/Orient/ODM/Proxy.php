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
 * Proxy class
 *
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author     David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM;

use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Proxy\AbstractProxy;

class Proxy extends AbstractProxy
{   
    protected $manager;
    protected $rid;
    protected $record;

    /**
     * Istantiates a new Proxy.
     *
     * @param Mapper $manager
     * @param string $rid 
     */
    function __construct(Manager $manager, $rid)
    {
        $this->manager = $manager;
        $this->rid    = $rid;
    }
    
    /**
     * Returns the record loaded with the Mapper.
     *
     * @return object
     */
    public function __invoke()
    {
        if ($this->record) {
            return $this->record;
        } else {
            $this->record = $this->getManager()->find($this->getRid());

            return $this->record;
        }    
    }
    
    /**
     * Returns the RID of the record to find.
     *
     * @return string
     */
    protected function getRid()
    {
        return $this->rid;
    }
}