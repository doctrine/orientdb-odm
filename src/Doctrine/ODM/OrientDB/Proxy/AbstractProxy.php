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
 * Class AbstractProxy
 *
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Proxy;

use Doctrine\ODM\OrientDB\ProxyInterface;

abstract class AbstractProxy implements ProxyInterface
{
    protected $manager;

    protected function getManager()
    {
        return $this->manager;
    }
}
