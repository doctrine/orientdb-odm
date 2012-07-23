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
 * This interface is used by the BindingInterface to return results from
 * the server.
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Congow\Orient\Contract\Binding;

interface BindingResultInterface
{
    /**
     * Returns the result set from the server.
     *
     * @return mixed
     */
    public function getResult();
}
