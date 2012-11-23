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
 * @package    Doctrine\ODM
 * @subpackage OrientDB
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Doctrine\ODM\OrientDB\Proxy;

use Doctrine\ODM\OrientDB\Mapper;

class Value extends AbstractProxy
{
    protected $value;

    /**
     * Sets the value of the proxy.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value associated with this proxy.
     *
     * @return mixed
     */
    public function __invoke()
    {
        return $this->value;
    }
}
