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

class Proxy
{   
    protected $mapper;
    protected $rid;
    protected $record;
    
    /**
     * @todo php doc proxy and collection
     * @todo create a Proxy interface for proxy and collection.
     */
    function __construct(Mapper $mapper, $rid)
    {
        $this->mapper = $mapper;
        $this->rid    = $rid;
    }
    
    public function __invoke()
    {
        if ($this->record) {
            return $this->record;
        } else {
            $this->record = $this->mapper->find($this->rid);
            return $this->record;
        }    
    }
}