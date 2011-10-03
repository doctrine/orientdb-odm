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
 * @subpackage Foundation
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Foundation\Types\Rid;

use Congow\Orient\Foundation\Types\Rid;

class Collection extends Rid
{
    protected $rids;
    
    public function __construct($rids)
    {
        $this->rids = $rids;
    }
    
    public function getValue()
    {
        return $this->rids;
    }
}