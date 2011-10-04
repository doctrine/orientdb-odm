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
 * Class LinkTracker
 *
 * @package     Orient
 * @subpackage  Mapper
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @author      David Funaro <ing.davidino@gmail.com>
 */

namespace Congow\Orient\ODM\Mapper;

class LinkTracker
{
    protected $properties = array();
    
    public function add($property, $value)
    {
        $this->properties[$property] = $value;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
}