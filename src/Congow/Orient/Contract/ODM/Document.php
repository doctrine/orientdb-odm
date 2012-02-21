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
 * Interface Document
 *
 * @package     Orient
 * @subpackage  Contract
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Contract\ODM;

interface Document
{
    /**
     * @todo phpdoc
     */
    public function getRid();
    
    /**
     * @todo phpdoc
     */
    public function setRid($rid);
}

