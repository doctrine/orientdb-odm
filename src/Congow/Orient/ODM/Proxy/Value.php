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
 * Class Value
 *
 * @package     
 * @subpackage  
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\ODM\Proxy;

use Congow\Orient\ODM\Mapper;

class Value
{
    protected $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function __invoke()
    {
        return $this->value;
    }
}

