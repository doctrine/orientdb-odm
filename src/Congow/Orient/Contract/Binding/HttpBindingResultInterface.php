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
 * HTTP bindings results set.
 *
 * @package    Congow\Orient
 * @subpackage Contract
 * @author     Daniele Alessandri <suppakilla@gmail.com>
 */

namespace Congow\Orient\Contract\Binding;

interface HttpBindingResultInterface extends BindingResultInterface
{
    /**
     * Returns the inner response object returned by the underlying client.
     *
     * @return mixed
     */
    public function getInnerResponse();

    /**
     * Returns if the response from the server is valid or has errors.
     *
     * @return boolean
     */
    public function isValid();
}
