<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Address
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace test\Integration\Document;

use Congow\Orient\ODM\Mapper\Annotations as ODM;
use Congow\Orient\Contract\ODM\Document;
/**
* @ODM\Document(class="Address")
*/
class Address implements Document
{
    protected $rid;
    /**
     * @ODM\Property(type="link")
     */
    protected $city;
    
    /**
     * @ODM\Property(name="type", type="string")
     */
    public $type;
    
    public function getCity()
    {
        return $this->city;
    }
    
    public function setCity($city)
    {
        $this->city = $city;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setRid($rid)
    {
        $this->rid = $rid;
    }
    
    public function getRid()
    {
        return $this->rid;
    }
    
    
}

