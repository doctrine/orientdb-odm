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
 * Rid class encapsulates a rid.
 *
 * @package    Congow\Orient
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Foundation\Types;


class Rid
{
    protected $rid;
    
    /**
     * Instantiates a new object, injecting the $rid;
     *
     * @param string $rid
     */
    public function __construct($rid)
    {
        $this->rid = $rid;
    }
    
    /**
     * Returns the rid associated with the current object.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->rid;
    }
}