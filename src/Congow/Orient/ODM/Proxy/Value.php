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
 * @package    Congow\Orient
 * @subpackage ODM
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\ODM\Proxy;

use Congow\Orient\ODM\Mapper;
use Congow\Orient\ODM\Proxy\AbstractProxy;

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
