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

use Congow\Orient\ODM\Mapper;

class Collection
{   
    protected $mapper;
    protected $rids;
    protected $collection;
    
    function __construct(Mapper $mapper, $rids)
    {
        $this->mapper = $mapper;
        $this->rids    = $rids;
    }
    
    public function __invoke()
    {
        if ($this->collection) {
            return $this->collection;
        } else {
            $this->collection = $this->mapper->findRecords($this->rids);
            return $this->collection;
        }    
    }
}